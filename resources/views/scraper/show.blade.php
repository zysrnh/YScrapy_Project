@extends('layouts.app', ['title' => 'Laporan Sentimen: ' . $job->target_query])

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-ys-muted mb-1">
                <a href="{{ route('scraper.index') }}" class="hover:text-white">&larr; Kembali ke Scraper</a>
                <span>/</span>
                <span class="text-ys-main">Laporan Sesi #{{ $job->id }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                "{{ $job->target_query }}"
            </h1>
            <p class="text-sm text-ys-muted mt-1">
                Platform: <span class="text-ys-main font-semibold uppercase">{{ $job->platform }}</span> &bull; 
                Dianalisis pada {{ $job->created_at->format('d M Y, H:i') }}
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('export.csv', $job->id) }}" class="glass-card px-4 py-2 rounded-md text-xs font-semibold text-white flex items-center space-x-2 hover:border-ys-main transition">
                <svg class="w-4 h-4 text-ys-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Download CSV Laporan</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics & Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Donut Visual -->
        <div class="glass-panel p-6 rounded-md">
            <h2 class="text-sm font-semibold text-white mb-2">Proporsi Sentimen Netizen</h2>
            <div class="h-48 flex items-center justify-center relative">
                <canvas id="jobSentimentChart"></canvas>
            </div>
            <div class="mt-4 pt-4 border-t border-ys-border/30 flex justify-around text-center text-xs">
                <div>
                    <div class="font-bold text-emerald-400">{{ $job->positive_count }}</div>
                    <div class="text-[10px] text-ys-muted">Positif ({{ $job->positive_percentage }}%)</div>
                </div>
                <div>
                    <div class="font-bold text-ys-main">{{ $job->neutral_count }}</div>
                    <div class="text-[10px] text-ys-muted">Netral ({{ $job->neutral_percentage }}%)</div>
                </div>
                <div>
                    <div class="font-bold text-rose-400">{{ $job->negative_count }}</div>
                    <div class="text-[10px] text-ys-muted">Negatif ({{ $job->negative_percentage }}%)</div>
                </div>
            </div>
        </div>

        <!-- Overall Verdict Card -->
        <div class="glass-panel p-6 rounded-md lg:col-span-2 flex flex-col justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-ys-muted">Kesimpulan Analisis Publik</span>
                <div class="mt-2 flex items-center space-x-3">
                    <span class="text-2xl font-bold text-white">Sentimen Dominan:</span>
                    @if($job->dominant_sentiment === 'positive')
                        <span class="px-3 py-1 rounded text-sm font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                            Cenderung Positif 👍
                        </span>
                    @elseif($job->dominant_sentiment === 'negative')
                        <span class="px-3 py-1 rounded text-sm font-bold bg-rose-500/20 text-rose-400 border border-rose-500/40">
                            Cenderung Negatif 👎
                        </span>
                    @else
                        <span class="px-3 py-1 rounded text-sm font-bold bg-ys-glass text-ys-main border border-ys-border">
                            Cenderung Netral / Berimbang ⚖️
                        </span>
                    @endif
                </div>

                <p class="mt-3 text-xs sm:text-sm text-ys-main/80 leading-relaxed">
                    Dari total <strong>{{ $job->total_scraped }} data opini</strong> yang dikumpulkan untuk topik ini, 
                    didapatkan skor rata-rata polaritas sebesar <strong>{{ $job->avg_sentiment_score }}</strong> 
                    (skala -1.0 s/d +1.0). 
                    @if($job->positive_count > $job->negative_count)
                        Mayoritas respon orang menunjukkan apresiasi, kepuasan, atau dukungan positif.
                    @elseif($job->negative_count > $job->positive_count)
                        Mayoritas respon orang menyoroti kritik, ketidakpuasan, atau keluhan terkait topik ini.
                    @else
                        Opini publik terbagi merata antara fakta objektif dan pertanyaan umum.
                    @endif
                </p>
            </div>

            <!-- Sentiment Progress Bar -->
            <div class="mt-6 pt-4 border-t border-ys-border/30">
                <div class="flex justify-between text-xs text-ys-muted mb-1.5">
                    <span>Breakdown Distribusi</span>
                    <span>Total: {{ $job->total_scraped }} feedback</span>
                </div>
                <div class="h-3 w-full bg-ys-surface rounded overflow-hidden flex">
                    <div style="width: {{ $job->positive_percentage }}%" class="bg-emerald-500" title="Positif: {{ $job->positive_percentage }}%"></div>
                    <div style="width: {{ $job->neutral_percentage }}%" class="bg-[#9BA8AB]" title="Netral: {{ $job->neutral_percentage }}%"></div>
                    <div style="width: {{ $job->negative_percentage }}%" class="bg-rose-500" title="Negatif: {{ $job->negative_percentage }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Feedback List -->
    <div class="glass-panel p-6 rounded-md space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-white">Detail Opini & Ulasan Terkumpul</h2>
                <p class="text-xs text-ys-muted">Menampilkan {{ $feedbacks->total() }} ulasan orang</p>
            </div>

            <!-- Filters Form -->
            <form action="{{ route('scraper.show', $job->id) }}" method="GET" class="flex flex-wrap items-center gap-2">
                <select name="sentiment" onchange="this.form.submit()" class="glass-input px-3 py-1.5 rounded-md text-xs">
                    <option value="">Semua Sentimen</option>
                    <option value="positive" {{ request('sentiment') === 'positive' ? 'selected' : '' }}>Positif</option>
                    <option value="neutral" {{ request('sentiment') === 'neutral' ? 'selected' : '' }}>Netral</option>
                    <option value="negative" {{ request('sentiment') === 'negative' ? 'selected' : '' }}>Negatif</option>
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dalam ulasan..."
                           class="glass-input pl-8 pr-3 py-1.5 rounded-md text-xs placeholder:text-ys-muted">
                    <svg class="w-3.5 h-3.5 text-ys-muted absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                @if(request()->hasAny(['sentiment', 'search']))
                    <a href="{{ route('scraper.show', $job->id) }}" class="text-xs text-ys-muted hover:text-white px-2 py-1">Reset</a>
                @endif
            </form>
        </div>

        <!-- Feedbacks Grid / List -->
        <div class="space-y-3">
            @forelse($feedbacks as $item)
                <div class="glass-card p-4 rounded-md">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-bold text-white">{{ $item->author_name }}</span>
                            @if($item->author_handle)
                                <span class="text-[11px] text-ys-muted">{{ $item->author_handle }}</span>
                            @endif
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-ys-glass border border-ys-border text-ys-muted">
                                {{ ucfirst($item->platform) }}
                            </span>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $item->sentiment_badge_class }}">
                                {{ $item->sentiment_label_indo }} ({{ $item->sentiment_score }})
                            </span>
                            @if($item->source_url)
                                <a href="{{ $item->source_url }}" target="_blank" rel="noopener" class="text-ys-muted hover:text-white transition" title="Buka tautan asli">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <p class="mt-2.5 text-xs sm:text-sm text-ys-main leading-relaxed">
                        "{{ $item->content_raw }}"
                    </p>

                    @if(!empty($item->sentiment_tokens))
                        <div class="mt-3 flex flex-wrap items-center gap-1.5 pt-2 border-t border-ys-border/20">
                            <span class="text-[10px] text-ys-muted font-medium">Kata kunci pemicu:</span>
                            @foreach($item->sentiment_tokens as $token)
                                <span class="text-[10px] px-2 py-0.5 rounded font-mono {{ ($token['weight'] ?? 0) > 0 ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-300 border border-rose-500/30' }}">
                                    {{ $token['word'] ?? '' }} 
                                    <span class="opacity-75">({{ ($token['weight'] ?? 0) > 0 ? '+' : '' }}{{ $token['weight'] ?? 0 }})</span>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-10 text-xs text-ys-muted">
                    Tidak ada ulasan yang cocok dengan filter.
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $feedbacks->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('jobSentimentChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Positif', 'Netral', 'Negatif'],
                    datasets: [{
                        data: [{{ $job->positive_count }}, {{ $job->neutral_count }}, {{ $job->negative_count }}],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(155, 168, 171, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: '#11212D',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    cutout: '70%'
                }
            });
        }
    });
</script>
@endpush
