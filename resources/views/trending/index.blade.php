@extends('layouts.app', ['title' => 'Isu Trending Hari Ini (Auto-Discovery)'])

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#1D1D1B]">Isu Viral & Trending Hari Ini (Indonesia)</h1>
            <p class="text-xs text-[#555552] mt-1">
                Topik terhangat yang sedang ramai dibahas di media nasional dan publik Indonesia. Klik untuk langsung menganalisis sentimen masyarakat.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <form action="{{ route('trending.scrapeAll') }}" method="POST">
                @csrf
                <button type="submit" class="neu-btn-primary px-4 py-2.5 text-xs font-medium flex items-center space-x-2" onclick="return confirm('Sistem akan men-scrape 4 isu teratas secara otomatis. Lanjutkan?');">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Scrape Top 4 Sekaligus</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Trending Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($trendingTopics as $topic)
            <div class="neu-card p-6 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between text-[11px] text-[#555552] mb-3">
                        <span class="font-bold uppercase tracking-wider text-[#1D1D1B]">{{ $topic['source'] }}</span>
                        <span>{{ $topic['published_at'] }}</span>
                    </div>

                    <h2 class="text-sm font-semibold text-[#1D1D1B] leading-snug group-hover:underline">
                        {{ $topic['title'] }}
                    </h2>
                </div>

                <div class="mt-6 pt-4 border-t border-[#c5c5ba]/40 flex items-center justify-between">
                    <span class="text-[11px] text-[#555552]">
                        Fokus: <strong class="text-[#1D1D1B]">{{ $topic['keyword'] }}</strong>
                    </span>

                    <form action="{{ route('trending.scrape') }}" method="POST">
                        @csrf
                        <input type="hidden" name="keyword" value="{{ $topic['keyword'] }}">
                        <input type="hidden" name="platform" value="all">
                        <button type="submit" class="neu-btn-primary px-3.5 py-1.5 text-xs font-medium flex items-center space-x-1.5">
                            <span>Analisis Sentimen</span>
                            <span>&rarr;</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-xs text-[#555552]">
                Tidak dapat memuat topik trending saat ini.
            </div>
        @endforelse
    </div>
</div>
@endsection
