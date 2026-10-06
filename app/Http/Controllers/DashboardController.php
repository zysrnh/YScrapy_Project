<?php

namespace App\Http\Controllers;

use App\Models\ScrapeJob;
use App\Models\ScrapedFeedback;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalJobs = ScrapeJob::count();
        $totalFeedbacks = ScrapedFeedback::count();
        $positiveCount = ScrapedFeedback::where('sentiment_label', 'positive')->count();
        $neutralCount = ScrapedFeedback::where('sentiment_label', 'neutral')->count();
        $negativeCount = ScrapedFeedback::where('sentiment_label', 'negative')->count();

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
            'recentJobs',
            'recentFeedbacks',
            'platforms'
        ));
    }
}
