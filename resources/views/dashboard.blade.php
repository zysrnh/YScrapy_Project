@extends('layouts.app', ['title' => 'Dashboard Sentimen Publik'])

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Dashboard Sentimen Publik</h1>
            <p class="text-sm text-ys-muted mt-1">Pemantauan persepsi, ulasan, dan komentar publik dari berbagai platform.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('export.csv') }}" class="glass-card px-3.5 py-2 rounded-md text-xs font-medium text-ys-main hover:text-white flex items-center space-x-2">
                <svg class="w-4 h-4 text-ys-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Export CSV</span>
            </a>
            <a href="{{ route('scraper.index') }}" class="glass-btn-primary px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-2">
                <svg class="w-4 h-4 text-ys-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Mulai Scraping Baru</span>
            </a>
        </div>
    </div>

    <!-- Metrics Cards (Glassmorphism, Minimal Rounded) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Scrapes -->
        <div class="glass-card p-5 rounded-md">
            <div class="flex items-center justify-between text-xs text-ys-muted">
                <span>Total Sesi Scrape</span>
                <span class="w-2 h-2 rounded-full bg-ys-border"></span>
            </div>
            <div class="mt-3 text-2xl font-bold text-white">{{ number_format($totalJobs) }}</div>
            <div class="mt-1 text-xs text-ys-muted">Sesi pencarian topik</div>
        </div>

        <!-- Total Feedbacks -->
        <div class="glass-card p-5 rounded-md">
            <div class="flex items-center justify-between text-xs text-ys-muted">
                <span>Total Data Publik</span>
                <span class="w-2 h-2 rounded-full bg-ys-muted"></span>
            </div>
            <div class="mt-3 text-2xl font-bold text-white">{{ number_format($totalFeedbacks) }}</div>
            <div class="mt-1 text-xs text-ys-muted">Komentar & ulasan orang</div>
        </div>

        <!-- Positive -->
        <div class="glass-card p-5 rounded-md border-emerald-500/20 bg-emerald-950/10">
            <div class="flex items-center justify-between text-xs text-emerald-400">
                <span>Sentimen Positif</span>
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            </div>
            <div class="mt-3 text-2xl font-bold text-emerald-300">
                {{ number_format($positiveCount) }}
                <span class="text-xs font-normal text-emerald-400/80">
                    ({{ $totalFeedbacks > 0 ? round(($positiveCount / $totalFeedbacks) * 100, 1) : 0 }}%)
                </span>
            </div>
            <div class="mt-1 text-xs text-emerald-400/60">Puas, mendukung, senang</div>
        </div>

        <!-- Neutral -->
        <div class="glass-card p-5 rounded-md border-ys-border/40">
            <div class="flex items-center justify-between text-xs text-ys-muted">
                <span>Sentimen Netral</span>
                <span class="w-2 h-2 rounded-full bg-ys-muted"></span>
            </div>
            <div class="mt-3 text-2xl font-bold text-ys-main">
                {{ number_format($neutralCount) }}
                <span class="text-xs font-normal text-ys-muted">
                    ({{ $totalFeedbacks > 0 ? round(($neutralCount / $totalFeedbacks) * 100, 1) : 0 }}%)
                </span>
            </div>
            <div class="mt-1 text-xs text-ys-muted">Informasi umum & objektif</div>
        </div>

        <!-- Negative -->
        <div class="glass-card p-5 rounded-md border-rose-500/20 bg-rose-950/10">
            <div class="flex items-center justify-between text-xs text-rose-400">
                <span>Sentimen Negatif</span>
                <span class="w-2 h-2 rounded-full bg-rose-400"></span>
            </div>
            <div class="mt-3 text-2xl font-bold text-rose-300">
                {{ number_format($negativeCount) }}
                <span class="text-xs font-normal text-rose-400/80">
                    ({{ $totalFeedbacks > 0 ? round(($negativeCount / $totalFeedbacks) * 100, 1) : 0 }}%)
                </span>
            </div>
            <div class="mt-1 text-xs text-rose-400/60">Komplain, kritik, kecewa</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Donut Chart: Rasio Sentimen -->
        <div class="glass-panel p-6 rounded-md">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-white">Distribusi Rasio Sentimen</h2>
                <span class="text-xs text-ys-muted">Persentase</span>
            </div>
            <div class="h-64 flex items-center justify-center relative">
                @if($totalFeedbacks > 0)
                    <canvas id="sentimentDonutChart"></canvas>
                @else
                    <div class="text-center text-xs text-ys-muted py-12">
                        Belum ada data sentimen. Jalankan scraping untuk melihat grafik.
                    </div>
                @endif
            </div>
        </div>

        <!-- Bar Chart: Sentimen Berdasarkan Platform -->
        <div class="glass-panel p-6 rounded-md lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-white">Sebaran Data per Platform</h2>
                <span class="text-xs text-ys-muted">Total data terkumpul</span>
            </div>
            <div class="h-64 flex items-center justify-center">
                @if($totalFeedbacks > 0)
                    <canvas id="platformBarChart"></canvas>
                @else
                    <div class="text-center text-xs text-ys-muted py-12">
                        Belum ada data platform yang di-scrape.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Scrape Sessions & Feedback Samples -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Scrapes List -->
        <div class="glass-panel p-6 rounded-md lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-white">Sesi Scraping Terkini</h2>
                <a href="{{ route('scraper.index') }}" class="text-xs text-ys-muted hover:text-white">Lihat Semua &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($recentJobs as $job)
                    <a href="{{ route('scraper.show', $job->id) }}" class="block glass-card p-3.5 rounded-md group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wider text-ys-muted">
                                {{ ucfirst($job->platform) }}
                            </span>
                            <span class="text-[11px] text-ys-muted">
                                {{ $job->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="mt-1 text-sm font-medium text-white group-hover:text-ys-main transition truncate">
                            "{{ $job->target_query }}"
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs text-ys-muted">
                            <span>{{ $job->total_scraped }} data</span>
                            <div class="flex items-center space-x-1.5">
                                <span class="px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 text-[10px]">{{ $job->positive_count }} pos</span>
                                <span class="px-1.5 py-0.5 rounded bg-rose-500/10 text-rose-400 text-[10px]">{{ $job->negative_count }} neg</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-xs text-ys-muted py-8">
                        Belum ada sesi scraping. Klik tombol <strong>Mulai Scraping Baru</strong> di atas!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Feedback Feeds -->
        <div class="glass-panel p-6 rounded-md lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-white">Opini & Respon Publik Terbaru</h2>
                <a href="{{ route('feedbacks.index') }}" class="text-xs text-ys-muted hover:text-white">Jelajahi Semua &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($recentFeedbacks as $item)
                    <div class="glass-card p-4 rounded-md">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-bold text-white">{{ $item->author_name }}</span>
                                <span class="text-[11px] text-ys-muted">({{ ucfirst($item->platform) }})</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $item->sentiment_badge_class }}">
                                {{ $item->sentiment_label_indo }} ({{ $item->sentiment_score }})
                            </span>
                        </div>
                        <p class="mt-2 text-xs sm:text-sm text-ys-main/90 leading-relaxed">
                            "{{ $item->content_raw }}"
                        </p>
                        @if(!empty($item->sentiment_tokens))
                            <div class="mt-2.5 flex flex-wrap items-center gap-1.5">
                                <span class="text-[10px] text-ys-muted">Keyword terdeteksi:</span>
                                @foreach($item->sentiment_tokens as $token)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded {{ ($token['weight'] ?? 0) > 0 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                        {{ $token['word'] ?? '' }} ({{ $token['weight'] ?? 0 }})
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-xs text-ys-muted py-8">
                        Belum ada komentar publik yang dianalisis.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($totalFeedbacks > 0)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Donut Chart Sentimen
        const ctxDonut = document.getElementById('sentimentDonutChart');
        if (ctxDonut) {
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Positif', 'Netral', 'Negatif'],
                    datasets: [{
                        data: [{{ $positiveCount }}, {{ $neutralCount }}, {{ $negativeCount }}],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.75)', // Positif Emerald
                            'rgba(155, 168, 171, 0.75)', // Netral Slate (#9BA8AB)
                            'rgba(239, 68, 68, 0.75)'   // Negatif Rose
                        ],
                        borderColor: '#11212D',
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#CCD0CF',
                                font: { family: 'Inter', size: 12 },
                                padding: 16
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }

        // 2. Bar Chart Platforms
        const ctxBar = document.getElementById('platformBarChart');
        if (ctxBar) {
            const platformLabels = {!! json_encode(array_map('ucfirst', array_keys($platforms))) !!};
            const platformData = {!! json_encode(array_values($platforms)) !!};

            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: platformLabels,
                    datasets: [{
                        label: 'Total Data Scraped',
                        data: platformData,
                        backgroundColor: 'rgba(74, 92, 106, 0.75)', // #4A5C6A
                        borderColor: '#9BA8AB',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#9BA8AB' },
                            grid: { color: 'rgba(74, 92, 106, 0.2)' }
                        },
                        x: {
                            ticks: { color: '#CCD0CF' },
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    });
</script>
@endif
@endpush
