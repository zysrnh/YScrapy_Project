@extends('layouts.app', ['title' => 'Semua Feedback & Sentimen'])

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Semua Feedback & Opini Publik</h1>
            <p class="text-sm text-ys-muted mt-1">Eksplorasi seluruh komentar, ulasan, gambar thumbnail, dan liputan berita dari seluruh platform.</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('export.csv') }}" class="glass-btn-primary px-4 py-2 rounded-md text-xs font-semibold flex items-center space-x-2">
                <svg class="w-4 h-4 text-ys-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Export Semua CSV</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="glass-panel p-5 rounded-md">
        <form action="{{ route('feedbacks.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
            <!-- Platform -->
            <div>
                <label class="block text-xs font-medium text-ys-muted mb-1.5">Filter Platform</label>
                <select name="platform" class="glass-input w-full px-3 py-2 rounded-md text-xs">
                    <option value="all">Semua Platform</option>
                    <option value="news" {{ request('platform') === 'news' ? 'selected' : '' }}>Portal Berita</option>
                    <option value="youtube" {{ request('platform') === 'youtube' ? 'selected' : '' }}>YouTube</option>
                    <option value="twitter" {{ request('platform') === 'twitter' ? 'selected' : '' }}>Twitter / X</option>
                    <option value="google_review" {{ request('platform') === 'google_review' ? 'selected' : '' }}>Google Review</option>
                    <option value="custom" {{ request('platform') === 'custom' ? 'selected' : '' }}>Direct URL</option>
                </select>
            </div>

            <!-- Sentiment -->
            <div>
                <label class="block text-xs font-medium text-ys-muted mb-1.5">Filter Sentimen</label>
                <select name="sentiment" class="glass-input w-full px-3 py-2 rounded-md text-xs">
                    <option value="all">Semua Sentimen</option>
                    <option value="positive" {{ request('sentiment') === 'positive' ? 'selected' : '' }}>Positif</option>
                    <option value="neutral" {{ request('sentiment') === 'neutral' ? 'selected' : '' }}>Netral</option>
                    <option value="negative" {{ request('sentiment') === 'negative' ? 'selected' : '' }}>Negatif</option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-xs font-medium text-ys-muted mb-1.5">Pencarian Teks / Pengirim</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kata kunci..."
                       class="glass-input w-full px-3 py-2 rounded-md text-xs placeholder:text-ys-muted">
            </div>

            <!-- Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="glass-btn-primary flex-1 py-2 px-3 rounded-md text-xs font-semibold">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['platform', 'sentiment', 'search']))
                    <a href="{{ route('feedbacks.index') }}" class="glass-card py-2 px-3 rounded-md text-xs text-ys-muted hover:text-white">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Feedbacks List with Thumbnails -->
    <div class="glass-panel p-6 rounded-md space-y-4">
        <div class="flex items-center justify-between text-xs text-ys-muted border-b border-ys-border/30 pb-3">
            <span>Ditemukan {{ $feedbacks->total() }} ulasan publik & liputan</span>
            <span>Halaman {{ $feedbacks->currentPage() }} dari {{ $feedbacks->lastPage() }}</span>
        </div>

        <div class="space-y-4">
            @forelse($feedbacks as $item)
                <div class="glass-card p-4 sm:p-5 rounded-md">
                    <div class="flex flex-col sm:flex-row items-start gap-4">
                        <!-- Thumbnail Image -->
                        <div class="w-full sm:w-28 h-32 sm:h-24 rounded-md overflow-hidden bg-ys-glass border border-ys-border/40 flex-shrink-0">
                            <img src="{{ $item->display_thumbnail }}" 
                                 alt="Thumbnail {{ $item->author_name }}" 
                                 class="w-full h-full object-cover"
                                 loading="lazy"
                                 onerror="this.src='https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=300&q=80'">
                        </div>

                        <!-- Content Body -->
                        <div class="flex-grow w-full space-y-2">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-white">{{ $item->author_name }}</span>
                                    @if($item->author_handle)
                                        <span class="text-[11px] text-ys-muted">{{ $item->author_handle }}</span>
                                    @endif
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-ys-glass border border-ys-border text-ys-muted uppercase">
                                        {{ ucfirst($item->platform) }}
                                    </span>
                                    @if($item->job)
                                        <a href="{{ route('scraper.show', $item->job->id) }}" class="text-[11px] text-ys-muted hover:text-white truncate max-w-xs">
                                            &bull; Sesi: "{{ $item->job->target_query }}"
                                        </a>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $item->sentiment_badge_class }}">
                                        {{ $item->sentiment_label_indo }} ({{ $item->sentiment_score }})
                                    </span>
                                    @if($item->source_url)
                                        <a href="{{ $item->source_url }}" target="_blank" rel="noopener" class="text-ys-muted hover:text-white transition" title="Lihat Tautan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <p class="text-xs sm:text-sm text-ys-main leading-relaxed">
                                "{{ $item->content_raw }}"
                            </p>

                            @if(!empty($item->sentiment_tokens))
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <span class="text-[10px] text-ys-muted font-medium">Kata kunci:</span>
                                    @foreach($item->sentiment_tokens as $token)
                                        <span class="text-[10px] px-2 py-0.5 rounded font-mono {{ ($token['weight'] ?? 0) > 0 ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-300 border border-rose-500/30' }}">
                                            {{ $token['word'] ?? '' }} ({{ $token['weight'] ?? 0 }})
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Toggle Full Content -->
                            @if(!empty($item->full_content) && strlen($item->full_content) > strlen($item->content_raw))
                                <div class="pt-2">
                                    <button type="button" 
                                            onclick="const el = document.getElementById('full-content-all-{{ $item->id }}'); el.classList.toggle('hidden'); this.querySelector('.arrow').classList.toggle('rotate-180');" 
                                            class="text-[11px] font-semibold text-ys-muted hover:text-white flex items-center space-x-1.5 transition">
                                        <span>Lihat Isi Konten & Liputan Lengkap</span>
                                        <svg class="w-3.5 h-3.5 arrow transition transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <div id="full-content-all-{{ $item->id }}" class="hidden mt-2 p-3 rounded bg-[#06141B]/80 border border-ys-border/40 text-xs text-ys-muted/90 whitespace-pre-line leading-relaxed font-sans">
                                        {{ $item->full_content }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-xs text-ys-muted">
                    Tidak ada ulasan yang ditemukan. Silakan jalankan scraping baru!
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $feedbacks->links() }}
        </div>
    </div>
</div>
@endsection
