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
    protected $description = 'Bersihkan teks konten feedback dan URL mentah di database';

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

            // Analisis ulang agar skor & tokennya bersih dari URL
            $res = $analyzer->analyze($clean);

            $f->update([
                'content_raw' => $clean,
                'content_clean' => $res['clean_text'],
                'sentiment_label' => $res['label'],
                'sentiment_score' => $res['score'],
                'sentiment_tokens' => $res['tokens'],
            ]);
        }

        $this->info('Selesai! Seluruh data teks ulasan di database sudah dibersihkan.');
        return 0;
    }
}
