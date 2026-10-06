<?php

namespace App\Http\Controllers;

use App\Models\ScrapeJob;
use App\Models\ScrapedFeedback;
use App\Models\Watchlist;
use App\Services\Trending\TrendingDiscoveryService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(TrendingDiscoveryService $trendingService)
    {
        $totalJobs = ScrapeJob::count();
        $totalFeedbacks = ScrapedFeedback::count();
        $positiveCount = ScrapedFeedback::where('sentiment_label', 'positive')->count();
        $neutralCount = ScrapedFeedback::where('sentiment_label', 'neutral')->count();
        $negativeCount = ScrapedFeedback::where('sentiment_label', 'negative')->count();

        $activeWatchlistsCount = Watchlist::where('is_active', true)->count();
        $topTrending = $trendingService->getTrendingTopics(3);

        $recentJobs = ScrapeJob::withCount('feedbacks')
            ->latest()
            ->take(5)
            ->get();

        $recentFeedbacks = ScrapedFeedback::with('job')
            ->latest()
            ->take(8)
            ->get();

        // Platform breakdown
        $platforms = ScrapedFeedback::selectRaw('platform, count(*) as total')
            ->groupBy('platform')
            ->pluck('total', 'platform')
            ->toArray();

        return view('dashboard', compact(
            'totalJobs',
            'totalFeedbacks',
            'positiveCount',
            'neutralCount',
            'negativeCount',
            'activeWatchlistsCount',
            'topTrending',
            'recentJobs',
            'recentFeedbacks',
            'platforms'
        ));
    }
}
