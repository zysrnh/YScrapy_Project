<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ExportController;

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

// Ekspor Data Sentimen ke CSV
Route::get('/export/csv/{jobId?}', [ExportController::class, 'exportCsv'])->name('export.csv');
