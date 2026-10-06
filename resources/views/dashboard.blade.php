@extends('layouts.app', ['title' => 'Dashboard Sentimen Publik'])

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 neu-entrance">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#1D1D1B]">Dashboard Sentimen Publik</h1>
            <p class="text-xs text-[#555552] mt-1 font-medium">Pemantauan persepsi opini dan ulasan publik lintas platform secara real-time.</p>
        </div>
        <div class="flex items-center space-x-2.5">
            <a href="{{ route('trending.index') }}" class="neu-btn-secondary px-4 py-2 text-xs font-semibold">
                Isu Trending
            </a>
            <a href="{{ route('watchlist.index') }}" class="neu-btn-secondary px-4 py-2 text-xs font-semibold">
                Watchlist ({{ $activeWatchlistsCount }})
            </a>
        </div>
    </div>

    <!-- Quick Trending Banner -->
    @if(!empty($topTrending))
        <div class="neu-card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 neu-entrance neu-delay-1">
            <div class="flex items-center space-x-3">
                <span class="text-[10px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-xl bg-[#1D1D1B] text-[#FFFFF7] shadow-sm">
                    Trending
                </span>
                <span class="text-xs text-[#1D1D1B] font-semibold truncate max-w-xl">
                    {{ $topTrending[0]['title'] ?? '' }}
                </span>
            </div>
            <div class="flex items-center space-x-2">
                <form action="{{ route('trending.scrape') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="keyword" value="{{ $topTrending[0]['keyword'] ?? '' }}">
                    <button type="submit" class="neu-btn-primary px-4 py-2 text-xs font-bold">
                        Analisis Isu Ini
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Metrics Cards (True Neumorphic + Staggered Animation) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <!-- Total Scrapes -->
        <div class="neu-card p-6 neu-entrance neu-delay-1">
            <div class="text-[11px] font-bold uppercase tracking-wider text-[#555552]">Total Sesi Scrape</div>
            <div class="mt-3 text-3xl font-extrabold tracking-tight text-[#1D1D1B]">{{ number_format($totalJobs) }}</div>
            <div class="mt-1.5 text-[11px] text-[#555552]">Sesi pencarian aktif</div>
        </div>

        <!-- Total Data -->
        <div class="neu-card p-6 neu-entrance neu-delay-2">
            <div class="text-[11px] font-bold uppercase tracking-wider text-[#555552]">Total Opini Terkumpul</div>
            <div class="mt-3 text-3xl font-extrabold tracking-tight text-[#1D1D1B]">{{ number_format($totalFeedbacks) }}</div>
            <div class="mt-1.5 text-[11px] text-[#555552]">Ulasan & berita terindeks</div>
        </div>

        <!-- Positive -->
        <div class="neu-card p-6 neu-entrance neu-delay-3">
            <div class="text-[11px] font-bold uppercase tracking-wider text-[#2E7D32]">Sentimen Positif</div>
            <div class="mt-3 text-3xl font-extrabold tracking-tight text-[#2E7D32]">
                {{ number_format($positiveCount) }}
                <span class="text-xs font-semibold text-[#555552]">
                    ({{ $totalFeedbacks > 0 ? round(($positiveCount / $totalFeedbacks) * 100, 1) : 0 }}%)
                </span>
            </div>
            <div class="mt-1.5 text-[11px] text-[#555552]">Apresiasi & respon baik</div>
        </div>

        <!-- Neutral -->
        <div class="neu-card p-6 neu-entrance neu-delay-4">
            <div class="text-[11px] font-bold uppercase tracking-wider text-[#555552]">Sentimen Netral</div>
            <div class="mt-3 text-3xl font-extrabold tracking-tight text-[#1D1D1B]">
                {{ number_format($neutralCount) }}
                <span class="text-xs font-semibold text-[#555552]">
                    ({{ $totalFeedbacks > 0 ? round(($neutralCount / $totalFeedbacks) * 100, 1) : 0 }}%)
                </span>
            </div>
            <div class="mt-1.5 text-[11px] text-[#555552]">Informasi umum berimbang</div>
        </div>

        <!-- Negative -->
        <div class="neu-card p-6 neu-entrance neu-delay-5">
            <div class="text-[11px] font-bold uppercase tracking-wider text-[#C62828]">Sentimen Negatif</div>
            <div class="mt-3 text-3xl font-extrabold tracking-tight text-[#C62828]">
                {{ number_format($negativeCount) }}
                <span class="text-xs font-semibold text-[#555552]">
                    ({{ $totalFeedbacks > 0 ? round(($negativeCount / $totalFeedbacks) * 100, 1) : 0 }}%)
                </span>
            </div>
            <div class="mt-1.5 text-[11px] text-[#555552]">Keluhan & kritik publik</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Donut Chart -->
        <div class="neu-card p-6 neu-entrance neu-delay-4">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-[#1D1D1B] mb-4">Distribusi Rasio Sentimen</h2>
            <div class="h-64 flex items-center justify-center relative">
                @if($totalFeedbacks > 0)
                    <canvas id="sentimentDonutChart"></canvas>
                @else
                    <div class="text-center text-xs text-[#555552]">Belum ada data sentimen.</div>
                @endif
            </div>
        </div>

        <!-- Bar Chart -->
        <div class="neu-card p-6 lg:col-span-2 neu-entrance neu-delay-5">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-[#1D1D1B] mb-4">Sebaran Data per Platform</h2>
            <div class="h-64 flex items-center justify-center">
                @if($totalFeedbacks > 0)
                    <canvas id="platformBarChart"></canvas>
                @else
                    <div class="text-center text-xs text-[#555552]">Belum ada data platform.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Lists Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Scrapes -->
        <div class="neu-card p-6 space-y-4 neu-entrance neu-delay-5">
            <div class="flex items-center justify-between pb-1">
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-[#1D1D1B]">Sesi Terkini</h2>
                <a href="{{ route('scraper.index') }}" class="text-xs font-bold text-[#555552] hover:text-[#1D1D1B]">Semua &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($recentJobs as $job)
                    <a href="{{ route('scraper.show', $job->id) }}" class="block p-4 neu-inset hover:opacity-90 transition">
                        <div class="flex items-center justify-between text-[11px] text-[#555552]">
                            <span class="font-extrabold uppercase tracking-wider">{{ $job->platform }}</span>
                            <span>{{ $job->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="mt-1.5 text-xs font-bold text-[#1D1D1B] truncate">
                            {{ $job->target_query }}
                        </div>
                        <div class="mt-2 flex items-center justify-between text-[11px]">
                            <span class="text-[#555552]">{{ $job->total_scraped }} data</span>
                            <div class="flex items-center space-x-1.5">
                                <span class="text-[#2E7D32] font-bold">{{ $job->positive_count }} pos</span>
                                <span>&bull;</span>
                                <span class="text-[#C62828] font-bold">{{ $job->negative_count }} neg</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-xs text-[#555552] py-6">Belum ada sesi tersimpan.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Feedbacks -->
        <div class="neu-card p-6 lg:col-span-2 space-y-4 neu-entrance neu-delay-6">
            <div class="flex items-center justify-between pb-1">
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-[#1D1D1B]">Opini & Berita Terbaru</h2>
                <a href="{{ route('feedbacks.index') }}" class="text-xs font-bold text-[#555552] hover:text-[#1D1D1B]">Jelajahi Semua &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($recentFeedbacks as $item)
                    <div class="p-4 neu-inset">
                        <div class="flex items-start gap-4">
                            <!-- Thumbnail / Avatar -->
                            <div class="w-14 h-14 rounded-2xl overflow-hidden bg-[#E7E7DD] flex-shrink-0 shadow-[2px_2px_5px_#c5c5ba,-2px_-2px_5px_#ffffff]">
                                @if($item->display_thumbnail)
                                    <img src="{{ $item->display_thumbnail }}" alt="{{ $item->author_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center font-bold text-xs text-[#555552]">
                                        {{ substr($item->author_name, 0, 2) }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2 text-xs">
                                        <span class="font-extrabold text-[#1D1D1B]">{{ $item->author_name }}</span>
                                        <span class="text-[#555552] text-[10px] uppercase font-bold tracking-wider">({{ $item->platform }})</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-xl text-[10px] font-bold {{ $item->sentiment_badge_class }}">
                                        {{ $item->sentiment_label_indo }} ({{ $item->sentiment_score }})
                                    </span>
                                </div>
                                <p class="text-xs text-[#1D1D1B] leading-relaxed line-clamp-2 font-medium">
                                    "{{ $item->content_raw }}"
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-xs text-[#555552] py-6">Belum ada opini publik yang dianalisis.</div>
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
        // Donut Chart with smooth animation
        const ctxDonut = document.getElementById('sentimentDonutChart');
        if (ctxDonut) {
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Positif', 'Netral', 'Negatif'],
                    datasets: [{
                        data: [{{ $positiveCount }}, {{ $neutralCount }}, {{ $negativeCount }}],
                        backgroundColor: ['#2E7D32', '#757575', '#C62828'],
                        borderColor: '#E7E7DD',
                        borderWidth: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 1200,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#1D1D1B',
                                font: { family: '"Plus Jakarta Sans"', size: 11, weight: '700' },
                                padding: 14
                            }
                        }
                    },
                    cutout: '72%'
                }
            });
        }

        // Bar Chart with smooth easeOutQuart animation
        const ctxBar = document.getElementById('platformBarChart');
        if (ctxBar) {
            const labels = {!! json_encode(array_map('ucfirst', array_keys($platforms))) !!};
            const data = {!! json_encode(array_values($platforms)) !!};

            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: '#1D1D1B',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#555552', font: { family: '"Plus Jakarta Sans"', size: 10 } },
                            grid: { color: 'rgba(197, 197, 186, 0.4)' }
                        },
                        x: {
                            ticks: { color: '#1D1D1B', font: { family: '"Plus Jakarta Sans"', size: 11, weight: '700' } },
                            grid: { display: false }
                        }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>
@endif
@endpush
