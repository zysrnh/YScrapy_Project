<?php

namespace App\Console\Commands;

use App\Models\Watchlist;
use App\Services\Scrapers\ScraperManager;
use Illuminate\Console\Command;

class RunScheduledScraperCommand extends Command
{
    protected $signature = 'yscrapy:run-scheduled-monitoring {--force : Jalankan semua tanpa memeriksa jadwal interval}';
    protected $description = 'Jalankan scraping otomatis untuk target topik yang terdaftar di Watchlist';

    public function handle(ScraperManager $scraperManager)
    {
        $this->info('Memulai pengecekan target Watchlist otomatis...');
        $force = $this->option('force');

        $watchlists = Watchlist::where('is_active', true)->get();

        if ($watchlists->isEmpty()) {
            $this->warn('Tidak ada target pantauan (Watchlist) yang aktif.');
            return 0;
        }

        $processed = 0;

        foreach ($watchlists as $item) {
            $isDue = $force || $this->isDue($item);

            if ($isDue) {
                $this->info("Menjalankan monitoring untuk: '{$item->keyword}' ({$item->platform})");
                try {
                    $scraperManager->execute($item->platform, $item->keyword, $item->limit_per_run);
                    $item->update([
                        'last_run_at' => now(),
                        'total_scraped_runs' => $item->total_scraped_runs + 1,
                    ]);
                    $processed++;
                } catch (\Throwable $e) {
                    $this->error("Gagal memproses {$item->keyword}: " . $e->getMessage());
                }
            } else {
                $this->line("Dilewati (belum masuk jadwal): '{$item->keyword}'");
            }
        }

        $this->info("Monitoring selesai! Total {$processed} target berhasil di-scrape otomatis.");
        return 0;
    }

    protected function isDue(Watchlist $item): bool
    {
        if (!$item->last_run_at) {
            return true;
        }

        $hoursPassed = $item->last_run_at->diffInHours(now());

        return match ($item->frequency) {
            'hourly' => $hoursPassed >= 1,
            'every_six_hours' => $hoursPassed >= 6,
            'daily' => $hoursPassed >= 24,
            default => $hoursPassed >= 1,
        };
    }
}
