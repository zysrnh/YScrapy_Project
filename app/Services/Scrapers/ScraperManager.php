<?php

namespace App\Services\Scrapers;

use App\Models\ScrapeJob;
use App\Models\ScrapedFeedback;
use App\Services\Sentiment\IndonesianSentimentAnalyzer;
use Illuminate\Support\Facades\DB;

class ScraperManager
{
    protected NewsScraper $newsScraper;
    protected YoutubeScraper $youtubeScraper;
    protected TwitterScraper $twitterScraper;
    protected GoogleReviewScraper $googleReviewScraper;
    protected DirectUrlScraper $directUrlScraper;
    protected IndonesianSentimentAnalyzer $sentimentAnalyzer;

    public function __construct(
        NewsScraper $newsScraper,
        YoutubeScraper $youtubeScraper,
        TwitterScraper $twitterScraper,
        GoogleReviewScraper $googleReviewScraper,
        DirectUrlScraper $directUrlScraper,
        IndonesianSentimentAnalyzer $sentimentAnalyzer
    ) {
        $this->newsScraper = $newsScraper;
        $this->youtubeScraper = $youtubeScraper;
        $this->twitterScraper = $twitterScraper;
        $this->googleReviewScraper = $googleReviewScraper;
        $this->directUrlScraper = $directUrlScraper;
        $this->sentimentAnalyzer = $sentimentAnalyzer;
    }

    /**
     * Jalankan proses scraping multi-platform dan langsung lakukan analisis sentimen.
     */
    public function execute(string $platform, string $query, int $limit = 20): ScrapeJob
    {
        $rawFeedbacks = [];
        $cleanTopic = NewsScraper::extractCleanTopic($query);

        if ($platform === 'all') {
            $perPlatformLimit = max(3, (int)ceil($limit / 4));
            $news = $this->newsScraper->scrape($query, $perPlatformLimit);
            $youtube = $this->youtubeScraper->scrape($query, $perPlatformLimit);
            $twitter = $this->twitterScraper->scrape($query, $perPlatformLimit);
            $reviews = $this->googleReviewScraper->scrape($query, $perPlatformLimit);

            $rawFeedbacks = array_merge($news, $youtube, $twitter, $reviews);
        } elseif ($platform === 'news') {
            $rawFeedbacks = $this->newsScraper->scrape($query, $limit);
        } elseif ($platform === 'youtube') {
            $rawFeedbacks = $this->youtubeScraper->scrape($query, $limit);
        } elseif ($platform === 'twitter') {
            $rawFeedbacks = $this->twitterScraper->scrape($query, $limit);
        } elseif ($platform === 'google_review') {
            $rawFeedbacks = $this->googleReviewScraper->scrape($query, $limit);
        } elseif ($platform === 'custom') {
            $rawFeedbacks = $this->directUrlScraper->scrape($query, $limit);
        } else {
            $rawFeedbacks = $this->newsScraper->scrape($query, $limit);
        }

        // Simpan ke Database dalam transaksi
        return DB::transaction(function () use ($platform, $query, $cleanTopic, $limit, $rawFeedbacks) {
            $job = ScrapeJob::create([
                'title' => 'Analisis Opini: ' . $cleanTopic,
                'platform' => $platform,
                'target_query' => $cleanTopic,
                'limit_requested' => $limit,
                'status' => 'processing',
            ]);

            $posCount = 0;
            $neuCount = 0;
            $negCount = 0;
            $totalScore = 0.0;
            $savedCount = 0;

            foreach ($rawFeedbacks as $item) {
                $rawContent = NewsScraper::sanitizeContent($item['content_raw'] ?? '');
                if (empty(trim($rawContent))) continue;

                // Eksekusi NLP sentiment analysis
                $analysis = $this->sentimentAnalyzer->analyze($rawContent);

                $label = $analysis['label'];
                $score = $analysis['score'];

                if ($label === 'positive') $posCount++;
                elseif ($label === 'negative') $negCount++;
                else $neuCount++;

                $totalScore += $score;
                $savedCount++;

                ScrapedFeedback::create([
                    'scrape_job_id' => $job->id,
                    'platform' => $item['platform'] ?? $platform,
                    'author_name' => $item['author_name'] ?? 'Netizen',
                    'author_handle' => $item['author_handle'] ?? null,
                    'content_raw' => $rawContent,
                    'content_clean' => $analysis['clean_text'] ?? null,
                    'source_url' => $item['source_url'] ?? null,
                    'sentiment_label' => $label,
                    'sentiment_score' => $score,
                    'sentiment_tokens' => $analysis['tokens'] ?? [],
                    'scraped_at' => $item['scraped_at'] ?? now(),
                ]);
            }

            $avgScore = $savedCount > 0 ? round($totalScore / $savedCount, 2) : 0.0;

            $job->update([
                'total_scraped' => $savedCount,
                'positive_count' => $posCount,
                'neutral_count' => $neuCount,
                'negative_count' => $negCount,
                'avg_sentiment_score' => $avgScore,
                'status' => 'completed',
            ]);

            return $job;
        });
    }
}
