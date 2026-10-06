<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\TrendingController;

// Dashboard Ringkasan Sentimen
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Scraper Engine & Sesi
Route::prefix('scraper')->name('scraper.')->group(function () {
    Route::get('/', [ScraperController::class, 'index'])->name('index');
    Route::post('/run', [ScraperController::class, 'store'])->name('store');
    Route::get('/{id}', [ScraperController::class, 'show'])->name('show');
    Route::delete('/{id}', [ScraperController::class, 'destroy'])->name('destroy');
});

// Penjelajah Opini & Komentar Publik
Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('feedbacks.index');

// Watchlist (Pemantauan Otomatis Berkala)
Route::prefix('watchlist')->name('watchlist.')->group(function () {
    Route::get('/', [WatchlistController::class, 'index'])->name('index');
    Route::post('/store', [WatchlistController::class, 'store'])->name('store');
    Route::patch('/{id}/toggle', [WatchlistController::class, 'toggle'])->name('toggle');
    Route::post('/{id}/run', [WatchlistController::class, 'runNow'])->name('run');
    Route::delete('/{id}', [WatchlistController::class, 'destroy'])->name('destroy');
});

// Auto-Trending Discovery (Isu Viral Indonesia)
Route::prefix('trending')->name('trending.')->group(function () {
    Route::get('/', [TrendingController::class, 'index'])->name('index');
    Route::post('/scrape', [TrendingController::class, 'scrapeTopic'])->name('scrape');
    Route::post('/scrape-all', [TrendingController::class, 'scrapeAll'])->name('scrapeAll');
});

// Ekspor Data Sentimen ke CSV
Route::get('/export/csv/{jobId?}', [ExportController::class, 'exportCsv'])->name('export.csv');
