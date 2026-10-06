@extends('layouts.app', ['title' => 'Semua Feedback & Sentimen'])

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 neu-entrance">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#1D1D1B]">Semua Feedback & Opini Publik</h1>
            <p class="text-xs text-[#555552] mt-1 font-medium">Eksplorasi seluruh komentar, ulasan, foto liputan, dan konten dari semua platform.</p>
        </div>
    </div>

    <!-- Filters & Search Bar (Neumorphic Card) -->
    <div class="neu-card p-6 sm:p-7 neu-entrance neu-delay-1">
        <form action="{{ route('feedbacks.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <!-- Platform -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#1D1D1B] mb-2">Filter Platform</label>
                <select name="platform" class="neu-input w-full px-3.5 py-2.5 text-xs font-medium">
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
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#1D1D1B] mb-2">Filter Sentimen</label>
                <select name="sentiment" class="neu-input w-full px-3.5 py-2.5 text-xs font-medium">
                    <option value="all">Semua Sentimen</option>
                    <option value="positive" {{ request('sentiment') === 'positive' ? 'selected' : '' }}>Positif</option>
                    <option value="neutral" {{ request('sentiment') === 'neutral' ? 'selected' : '' }}>Netral</option>
                    <option value="negative" {{ request('sentiment') === 'negative' ? 'selected' : '' }}>Negatif</option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#1D1D1B] mb-2">Pencarian Teks / Pengirim</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kata kunci..."
                       class="neu-input w-full px-3.5 py-2.5 text-xs placeholder:text-[#555552]/70 font-medium">
            </div>

            <!-- Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="neu-btn-primary flex-1 py-2.5 px-4 text-xs font-bold">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['platform', 'sentiment', 'search']))
                    <a href="{{ route('feedbacks.index') }}" class="neu-btn-secondary py-2.5 px-3.5 text-xs font-bold">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Feedbacks List with Real Thumbnails & Dummy Avatars -->
    <div class="neu-card p-6 sm:p-8 space-y-4 neu-entrance neu-delay-2">
        <div class="flex items-center justify-between text-xs text-[#555552] pb-3 border-b border-[#c5c5ba]/40 font-medium">
            <span>Ditemukan {{ $feedbacks->total() }} ulasan publik & liputan</span>
            <span>Halaman {{ $feedbacks->currentPage() }} dari {{ $feedbacks->lastPage() }}</span>
        </div>

        <div class="space-y-4">
            @forelse($feedbacks as $item)
                <div class="p-5 neu-subcard">
                    <div class="flex flex-col sm:flex-row items-start gap-4">
                        <!-- Foto Thumbnail Asli (Jika Berita) atau Avatar Inisial (Jika Netizen) -->
                        <div class="w-full sm:w-28 h-32 sm:h-24 rounded-2xl overflow-hidden bg-[#E7E7DD] flex-shrink-0 shadow-[2px_2px_5px_#c5c5ba,-2px_-2px_5px_#ffffff]">
                            @if($item->display_thumbnail)
                                <img src="{{ $item->display_thumbnail }}" 
                                     alt="{{ $item->author_name }}" 
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center font-extrabold text-xs text-[#555552] bg-[#E7E7DD]">
                                    {{ substr($item->author_name, 0, 2) }}
                                </div>
                            @endif
                        </div>

                        <!-- Content Body -->
                        <div class="flex-grow w-full space-y-2">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-extrabold text-[#1D1D1B]">{{ $item->author_name }}</span>
                                    @if($item->author_handle)
                                        <span class="text-[11px] text-[#555552] font-medium">{{ $item->author_handle }}</span>
                                    @endif
                                    <span class="text-[10px] px-2 py-0.5 rounded-lg neu-inset text-[#1D1D1B] uppercase font-bold tracking-wider">
                                        {{ $item->platform }}
                                    </span>
                                    @if($item->job)
                                        <a href="{{ route('scraper.show', $item->job->id) }}" class="text-[11px] text-[#555552] hover:text-[#1D1D1B] truncate max-w-xs font-medium">
                                            &bull; Sesi: "{{ $item->job->target_query }}"
                                        </a>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-0.5 rounded-xl text-[10px] font-bold {{ $item->sentiment_badge_class }}">
                                        {{ $item->sentiment_label_indo }} ({{ $item->sentiment_score }})
                                    </span>
                                    @if($item->source_url)
                                        <a href="{{ $item->source_url }}" target="_blank" rel="noopener" class="text-xs font-semibold text-[#555552] hover:text-[#1D1D1B]" title="Buka tautan asli">
                                            Tautan Asli &rarr;
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <p class="text-xs text-[#1D1D1B] leading-relaxed font-semibold">
                                "{{ $item->content_raw }}"
                            </p>

                            <!-- Accordion Paragraf Berita Lengkap -->
                            @if(!empty($item->full_content) && strlen($item->full_content) > strlen($item->content_raw))
                                <div class="pt-1.5">
                                    <button type="button" 
                                            onclick="const el = document.getElementById('full-content-all-{{ $item->id }}'); el.classList.toggle('hidden');" 
                                            class="text-[11px] font-bold text-[#555552] hover:text-[#1D1D1B] underline">
                                        Baca Isi Paragraf Berita / Konten Lengkap
                                    </button>

                                    <div id="full-content-all-{{ $item->id }}" class="hidden mt-2 p-4 neu-inset text-xs text-[#1D1D1B] whitespace-pre-line leading-relaxed font-medium">
                                        {{ $item->full_content }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-xs text-[#555552]">
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
