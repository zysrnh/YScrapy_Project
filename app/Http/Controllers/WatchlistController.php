<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Services\Scrapers\ScraperManager;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function index()
    {
        $watchlists = Watchlist::latest()->paginate(10);
        return view('watchlist.index', compact('watchlists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255',
            'platform' => 'required|string|in:all,news,youtube,twitter,google_review',
            'frequency' => 'required|string|in:hourly,every_six_hours,daily',
            'limit_per_run' => 'required|integer|min:5|max:60',
        ]);

        Watchlist::create([
            'keyword' => $validated['keyword'],
            'platform' => $validated['platform'],
            'frequency' => $validated['frequency'],
            'limit_per_run' => (int)$validated['limit_per_run'],
            'is_active' => true,
        ]);

        return redirect()->route('watchlist.index')->with('success', "Target pantauan '{$validated['keyword']}' berhasil ditambahkan ke Watchlist otomatis.");
    }

    public function toggle($id)
    {
        $watchlist = Watchlist::findOrFail($id);
        $watchlist->update([
            'is_active' => !$watchlist->is_active,
        ]);

        $status = $watchlist->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('watchlist.index')->with('success', "Target '{$watchlist->keyword}' berhasil {$status}.");
    }

    public function runNow(ScraperManager $scraperManager, $id)
    {
        $watchlist = Watchlist::findOrFail($id);

        try {
            $job = $scraperManager->execute($watchlist->platform, $watchlist->keyword, $watchlist->limit_per_run);
            $watchlist->update([
                'last_run_at' => now(),
                'total_scraped_runs' => $watchlist->total_scraped_runs + 1,
            ]);

            return redirect()->route('scraper.show', $job->id)->with('success', "Scraping pantauan untuk '{$watchlist->keyword}' berhasil dijalankan!");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menjalankan scraping: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $watchlist = Watchlist::findOrFail($id);
        $watchlist->delete();

        return redirect()->route('watchlist.index')->with('success', 'Target pantauan berhasil dihapus.');
    }
}
