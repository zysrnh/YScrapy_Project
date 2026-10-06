@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#1D1D1B]">Dashboard Sentimen Publik</h1>
            <p class="text-xs text-[#555552] mt-1">Pemantauan persepsi opini dan ulasan publik lintas platform secara terstruktur.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('trending.index') }}" class="neu-btn-secondary px-3.5 py-1.5 text-xs font-medium">
                Isu Trending
            </a>
            <a href="{{ route('watchlist.index') }}" class="neu-btn-secondary px-3.5 py-1.5 text-xs font-medium">
                Watchlist ({{ $activeWatchlistsCount }})
            </a>
            <a href="{{ route('export.csv') }}" class="neu-btn-secondary px-3.5 py-1.5 text-xs font-medium">
                Export CSV
            </a>
            <a href="{{ route('scraper.index') }}" class="neu-btn-primary px-3.5 py-1.5 text-xs font-medium">
                Scrape Baru
            </a>
        </div>
    </div>

    <!-- Quick Trending Banner -->
    @if(!empty($topTrending))
        <div class="neu-card p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-2.5">
                <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-[#1D1D1B] text-[#FFFFF7]">
                    Trending
                </span>
                <span class="text-xs text-[#1D1D1B] font-medium truncate max-w-lg">
                    {{ $topTrending[0]['title'] ?? '' }}
                </span>
            </div>
            <div class="flex items-center space-x-2">
                <form action="{{ route('trending.scrape') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="keyword" value="{{ $topTrending[0]['keyword'] ?? '' }}">
                    <button type="submit" class="neu-btn-primary px-3 py-1 text-xs">
                        Analisis Isu Ini
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Metrics Cards (Neumorphic) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Scrapes -->
        <div class="neu-card p-5">
            <div class="text-xs text-[#555552]">Total Sesi Scrape</div>
            <div class="mt-2 text-2xl font-bold text-[#1D1D1B]">{{ number_format($totalJobs) }}</div>
            <div class="mt-1 text-[11px] text-[#555552]">Sesi pencarian aktif</div>
        </div>

        <!-- Total Data -->
        <div class="neu-card p-5">
            <div class="text-xs text-[#555552]">Total Data Opini</div>
            <div class="mt-2 text-2xl font-bold text-[#1D1D1B]">{{ number_format($totalFeedbacks) }}</div>
            <div class="mt-1 text-[11px] text-[#555552]">Ulasan & konten terkumpul</div>
        </div>

        <!-- Positive -->
        <div class="neu-card p-5 border-l-4 border-l-[#2E7D32]">
            <div class="text-xs text-[#2E7D32] font-semibold">Sentimen Positif</div>
            <div class="mt-2 text-2xl font-bold text-[#2E7D32]">
                {{ number_format($positiveCount) }}
                <span class="text-xs font-normal opacity-80">
                    ({{ $totalFeedbacks > 0 ? round(($positiveCount / $totalFeedbacks) * 100, 1) : 0 }}%)
                </span>
            </div>
            <div class="mt-1 text-[11px] text-[#555552]">Apresiasi & kepuasan</div>
        </div>

        <!-- Neutral -->
        <div class="neu-card p-5 border-l-4 border-l-[#555552]">
            <div class="text-xs text-[#555552] font-semibold">Sentimen Netral</div>
            <div class="mt-2 text-2xl font-bold text-[#1D1D1B]">
                {{ number_format($neutralCount) }}
                <span class="text-xs font-normal text-[#555552]">
                    ({{ $totalFeedbacks > 0 ? round(($neutralCount / $totalFeedbacks) * 100, 1) : 0 }}%)
                </span>
            </div>
            <div class="mt-1 text-[11px] text-[#555552]">Informasi umum objektif</div>
        </div>

        <!-- Negative -->
        <div class="neu-card p-5 border-l-4 border-l-[#C62828]">
            <div class="text-xs text-[#C62828] font-semibold">Sentimen Negatif</div>
            <div class="mt-2 text-2xl font-bold text-[#C62828]">
                {{ number_format($negativeCount) }}
                <span class="text-xs font-normal opacity-80">
                    ({{ $totalFeedbacks > 0 ? round(($negativeCount / $totalFeedbacks) * 100, 1) : 0 }}%)
                </span>
            </div>
            <div class="mt-1 text-[11px] text-[#555552]">Keluhan & kritik publik</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Donut Chart -->
        <div class="neu-card p-6">
            <h2 class="text-sm font-bold text-[#1D1D1B] mb-4">Distribusi Rasio Sentimen</h2>
            <div class="h-64 flex items-center justify-center relative">
                @if($totalFeedbacks > 0)
                    <canvas id="sentimentDonutChart"></canvas>
                @else
                    <div class="text-center text-xs text-[#555552]">Belum ada data sentimen.</div>
                @endif
            </div>
        </div>

        <!-- Bar Chart -->
        <div class="neu-card p-6 lg:col-span-2">
            <h2 class="text-sm font-bold text-[#1D1D1B] mb-4">Sebaran Data per Platform</h2>
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
        <div class="neu-card p-6 space-y-3">
            <div class="flex items-center justify-between border-b border-[#E7E7DD] pb-2">
                <h2 class="text-sm font-bold text-[#1D1D1B]">Sesi Terkini</h2>
                <a href="{{ route('scraper.index') }}" class="text-xs text-[#555552] hover:text-[#1D1D1B]">Semua &rarr;</a>
            </div>

            <div class="space-y-2.5">
                @forelse($recentJobs as $job)
                    <a href="{{ route('scraper.show', $job->id) }}" class="block p-3 rounded neu-inset hover:opacity-90 transition">
                        <div class="flex items-center justify-between text-[11px] text-[#555552]">
                            <span class="font-semibold uppercase">{{ $job->platform }}</span>
                            <span>{{ $job->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="mt-1 text-xs font-semibold text-[#1D1D1B] truncate">
                            {{ $job->target_query }}
                        </div>
                        <div class="mt-1.5 flex items-center justify-between text-[11px]">
                            <span class="text-[#555552]">{{ $job->total_scraped }} data</span>
                            <div class="flex items-center space-x-1">
                                <span class="text-[#2E7D32] font-semibold">{{ $job->positive_count }} pos</span>
                                <span>&bull;</span>
                                <span class="text-[#C62828] font-semibold">{{ $job->negative_count }} neg</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-xs text-[#555552] py-6">Belum ada sesi tersimpan.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Feedbacks -->
        <div class="neu-card p-6 lg:col-span-2 space-y-3">
            <div class="flex items-center justify-between border-b border-[#E7E7DD] pb-2">
                <h2 class="text-sm font-bold text-[#1D1D1B]">Ulasan & Opini Publik Terbaru</h2>
                <a href="{{ route('feedbacks.index') }}" class="text-xs text-[#555552] hover:text-[#1D1D1B]">Jelajahi Semua &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($recentFeedbacks as $item)
                    <div class="p-3.5 rounded neu-inset">
                        <div class="flex items-start gap-3">
                            <!-- Thumbnail / Avatar -->
                            <div class="w-14 h-14 rounded overflow-hidden bg-[#E7E7DD] border border-[#D5D5CA] flex-shrink-0">
                                @if($item->display_thumbnail)
                                    <img src="{{ $item->display_thumbnail }}" alt="{{ $item->author_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center font-bold text-xs text-[#555552]">
                                        {{ substr($item->author_name, 0, 2) }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow space-y-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-1.5 text-xs">
                                        <span class="font-bold text-[#1D1D1B]">{{ $item->author_name }}</span>
                                        <span class="text-[#555552]">({{ ucfirst($item->platform) }})</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $item->sentiment_badge_class }}">
                                        {{ $item->sentiment_label_indo }} ({{ $item->sentiment_score }})
                                    </span>
                                </div>
                                <p class="text-xs text-[#1D1D1B] leading-relaxed line-clamp-2">
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
        // Donut Chart
        const ctxDonut = document.getElementById('sentimentDonutChart');
        if (ctxDonut) {
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Positif', 'Netral', 'Negatif'],
                    datasets: [{
                        data: [{{ $positiveCount }}, {{ $neutralCount }}, {{ $negativeCount }}],
                        backgroundColor: ['#2E7D32', '#757575', '#C62828'],
                        borderColor: '#FFFFF7',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#1D1D1B',
                                font: { family: 'Inter', size: 11 },
                                padding: 12
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // Bar Chart
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
                        borderRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#555552' },
                            grid: { color: '#E7E7DD' }
                        },
                        x: {
                            ticks: { color: '#1D1D1B' },
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
