<?php

namespace App\Http\Controllers;

use App\Services\Trending\TrendingDiscoveryService;
use App\Services\Scrapers\ScraperManager;
use Illuminate\Http\Request;

class TrendingController extends Controller
{
    protected TrendingDiscoveryService $trendingService;

    public function __construct(TrendingDiscoveryService $trendingService)
    {
        $this->trendingService = $trendingService;
    }

    public function index()
    {
        $trendingTopics = $this->trendingService->getTrendingTopics(12);
        return view('trending.index', compact('trendingTopics'));
    }

    public function scrapeTopic(Request $request, ScraperManager $scraperManager)
    {
        $validated = $request->validate([
            'keyword' => 'required|string',
            'platform' => 'nullable|string|in:all,news,youtube,twitter,google_review',
        ]);

        $platform = $validated['platform'] ?? 'all';

        try {
            $job = $scraperManager->execute($platform, $validated['keyword'], 15);
            return redirect()->route('scraper.show', $job->id)->with('success', "Isu trending '{$validated['keyword']}' berhasil di-scrape dan dianalisis!");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memproses isu trending: ' . $e->getMessage());
        }
    }

    public function scrapeAll(ScraperManager $scraperManager)
    {
        $trendingTopics = $this->trendingService->getTrendingTopics(4);
        $scrapedCount = 0;

        foreach ($trendingTopics as $topic) {
            try {
                $scraperManager->execute('all', $topic['keyword'], 10);
                $scrapedCount++;
            } catch (\Throwable $e) {
                // lanjut ke topik berikutnya jika ada yang timeout
            }
        }

        return redirect()->route('dashboard')->with('success', "Berhasil menganalisis {$scrapedCount} isu trending nasional teratas hari ini!");
    }
}
