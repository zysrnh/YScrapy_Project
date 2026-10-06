<?php

namespace App\Http\Controllers;

use App\Models\ScrapeJob;
use App\Models\ScrapedFeedback;
use App\Services\Scrapers\ScraperManager;
use Illuminate\Http\Request;

class ScraperController extends Controller
{
    protected ScraperManager $scraperManager;

    public function __construct(ScraperManager $scraperManager)
    {
        $this->scraperManager = $scraperManager;
    }

    public function index()
    {
        $jobs = ScrapeJob::withCount('feedbacks')->latest()->paginate(10);
        return view('scraper.index', compact('jobs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|in:all,news,youtube,twitter,google_review,custom',
            'query' => 'required|string|max:500',
            'limit' => 'required|integer|min:3|max:100',
        ]);

        try {
            $job = $this->scraperManager->execute(
                $validated['platform'],
                $validated['query'],
                (int)$validated['limit']
            );

            return redirect()->route('scraper.show', $job->id)->with('success', "Scraping selesai! {$job->total_scraped} data opini & sentimen berhasil dianalisis.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memproses scraping: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, $id)
    {
        $job = ScrapeJob::findOrFail($id);

        $query = $job->feedbacks();

        if ($request->filled('sentiment')) {
            $query->where('sentiment_label', $request->sentiment);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content_raw', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        $feedbacks = $query->latest()->paginate(15)->withQueryString();

        return view('scraper.show', compact('job', 'feedbacks'));
    }

    public function destroy($id)
    {
        $job = ScrapeJob::findOrFail($id);
        $job->delete();

        return redirect()->route('scraper.index')->with('success', 'Sesi scraping berhasil dihapus.');
    }
}
