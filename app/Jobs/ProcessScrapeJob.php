<?php

namespace App\Jobs;

use App\Services\Scrapers\ScraperManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessScrapeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $platform;
    public string $query;
    public int $limit;

    /**
     * Create a new job instance.
     */
    public function __construct(string $platform, string $query, int $limit = 20)
    {
        $this->platform = $platform;
        $this->query = $query;
        $this->limit = $limit;
    }

    /**
     * Execute the job.
     */
    public function handle(ScraperManager $scraperManager): void
    {
        $scraperManager->execute($this->platform, $this->query, $this->limit);
    }

    /**
     * Handle job failure.
     */
    public function failed(?Throwable $exception): void
    {
        \Log::error("Scrape Job failed for query '{$this->query}': " . ($exception ? $exception->getMessage() : 'Unknown error'));
    }
}
