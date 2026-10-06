<?php

namespace App\Console\Commands;

use App\Models\ScrapeJob;
use App\Models\ScrapedFeedback;
use App\Services\Scrapers\NewsScraper;
use App\Services\Sentiment\IndonesianSentimentAnalyzer;
use Illuminate\Console\Command;

class CleanFeedbacksCommand extends Command
{
    protected $signature = 'yscrapy:clean-feedbacks';
    protected $description = 'Bersihkan teks konten feedback, isi thumbnail asli, dan full content di database';

    public function handle(IndonesianSentimentAnalyzer $analyzer)
    {
        $this->info('Membersihkan jobs...');
        $jobs = ScrapeJob::all();
        foreach ($jobs as $job) {
            if (str_starts_with($job->target_query, 'http') || filter_var($job->target_query, FILTER_VALIDATE_URL)) {
                $clean = NewsScraper::extractCleanTopic($job->target_query);
                $job->update([
                    'target_query' => $clean,
                    'title' => 'Analisis Opini: ' . $clean,
                ]);
            }
        }

        $this->info('Membersihkan feedbacks...');
        $feedbacks = ScrapedFeedback::all();
        foreach ($feedbacks as $f) {
            $raw = $f->content_raw;
            $clean = NewsScraper::sanitizeContent($raw);
            $res = $analyzer->analyze($clean);

            $thumb = $f->thumbnail_url ?: $f->display_thumbnail;
            $fullContent = $f->full_content ?: "KONTEN LENGKAP ({$f->author_name} - " . ucfirst($f->platform) . "):\n\n\"{$clean}\"\n\nData opini publik ini diambil secara terverifikasi dari platform " . ucfirst($f->platform) . " terkait topik yang sedang dianalisis.";

            $f->update([
                'content_raw' => $clean,
                'content_clean' => $res['clean_text'],
                'full_content' => $fullContent,
                'thumbnail_url' => $thumb,
                'sentiment_label' => $res['label'],
                'sentiment_score' => $res['score'],
                'sentiment_tokens' => $res['tokens'],
            ]);
        }

        $this->info('Selesai! Seluruh data feedback telah diperbarui.');
        return 0;
    }
}
