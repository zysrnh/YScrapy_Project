<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan 1 sesi scraping sample otomatis agar dashboard ada datanya
        $scraperManager = app(\App\Services\Scrapers\ScraperManager::class);
        $scraperManager->execute('all', 'Layanan Paspor Online M-Paspor', 16);

        // Tambah contoh target Watchlist
        \App\Models\Watchlist::firstOrCreate(
            ['keyword' => 'Timnas Indonesia'],
            [
                'platform' => 'all',
                'frequency' => 'hourly',
                'limit_per_run' => 20,
                'is_active' => true,
                'total_scraped_runs' => 1,
                'last_run_at' => now(),
            ]
        );

        \App\Models\Watchlist::firstOrCreate(
            ['keyword' => 'Kebijakan Pajak PPN'],
            [
                'platform' => 'news',
                'frequency' => 'daily',
                'limit_per_run' => 15,
                'is_active' => true,
                'total_scraped_runs' => 0,
                'last_run_at' => null,
            ]
        );
    }
}
