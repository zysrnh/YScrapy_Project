<?php

namespace App\Http\Controllers;

use App\Models\ScrapedFeedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = ScrapedFeedback::with('job');

        if ($request->filled('platform') && $request->platform !== 'all') {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('sentiment') && $request->sentiment !== 'all') {
            $query->where('sentiment_label', $request->sentiment);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content_raw', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        $feedbacks = $query->latest()->paginate(20)->withQueryString();

        return view('feedbacks.index', compact('feedbacks'));
    }
}
