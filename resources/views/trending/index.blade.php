@extends('layouts.app', ['title' => 'Isu Trending Hari Ini (Auto-Discovery)'])

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white flex items-center space-x-2">
                <span>🔥</span>
                <span>Isu Viral & Trending Hari Ini (Indonesia)</span>
            </h1>
            <p class="text-sm text-ys-muted mt-1">
                Topik terpanas yang sedang ramai dibahas di media nasional dan publik Indonesia. Klik untuk langsung menganalisis sentimen masyarakat.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <form action="{{ route('trending.scrapeAll') }}" method="POST">
                @csrf
                <button type="submit" class="glass-btn-primary px-4 py-2 rounded-md text-xs font-semibold flex items-center space-x-2" onclick="return confirm('Sistem akan men-scrape 4 isu teratas secara otomatis. Lanjutkan?');">
                    <svg class="w-4 h-4 text-ys-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Scrape Top 4 Sekaligus</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Trending Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($trendingTopics as $topic)
            <div class="glass-panel p-5 rounded-md flex flex-col justify-between hover:border-ys-muted transition group">
                <div>
                    <div class="flex items-center justify-between text-[11px] text-ys-muted mb-2">
                        <span class="font-medium text-ys-main">{{ $topic['source'] }}</span>
                        <span>{{ $topic['published_at'] }}</span>
                    </div>

                    <h2 class="text-sm sm:text-base font-semibold text-white leading-snug group-hover:text-ys-main transition">
                        {{ $topic['title'] }}
                    </h2>
                </div>

                <div class="mt-5 pt-3 border-t border-ys-border/30 flex items-center justify-between">
                    <span class="text-[11px] text-ys-muted">
                        Fokus: <strong class="text-white">{{ $topic['keyword'] }}</strong>
                    </span>

                    <form action="{{ route('trending.scrape') }}" method="POST">
                        @csrf
                        <input type="hidden" name="keyword" value="{{ $topic['keyword'] }}">
                        <input type="hidden" name="platform" value="all">
                        <button type="submit" class="px-3 py-1.5 rounded-md text-xs font-semibold bg-ys-glass border border-ys-border text-white hover:bg-ys-border/60 transition flex items-center space-x-1.5">
                            <span>Analisis Sentimen</span>
                            <span>&rarr;</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-xs text-ys-muted">
                Tidak dapat memuat topik trending saat ini.
            </div>
        @endforelse
    </div>
</div>
@endsection
