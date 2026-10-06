@extends('layouts.app', ['title' => 'Laporan: ' . $job->target_query])

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-1.5 text-xs text-[#555552] mb-1">
                <a href="{{ route('scraper.index') }}" class="hover:text-[#1D1D1B]">&larr; Kembali ke Scraper</a>
                <span>/</span>
                <span>Laporan #{{ $job->id }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[#1D1D1B]">
                {{ $job->target_query }}
            </h1>
            <p class="text-xs text-[#555552] mt-0.5">
                Platform: <span class="font-bold uppercase text-[#1D1D1B]">{{ $job->platform }}</span> &bull; 
                Dianalisis: {{ $job->created_at->format('d M Y, H:i') }}
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('export.csv', $job->id) }}" class="neu-btn-secondary px-3.5 py-1.5 text-xs font-semibold">
                Download CSV
            </a>
        </div>
    </div>

    <!-- Summary Metrics & Verdict -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Donut Visual -->
        <div class="neu-card p-6">
            <h2 class="text-xs font-bold uppercase tracking-wider text-[#555552] mb-2">Rasio Sentimen Publik</h2>
            <div class="h-44 flex items-center justify-center relative">
                <canvas id="jobSentimentChart"></canvas>
            </div>
            <div class="mt-4 pt-3 border-t border-[#E7E7DD] flex justify-around text-center text-xs">
                <div>
                    <div class="font-bold text-[#2E7D32]">{{ $job->positive_count }}</div>
                    <div class="text-[10px] text-[#555552]">Positif ({{ $job->positive_percentage }}%)</div>
                </div>
                <div>
                    <div class="font-bold text-[#1D1D1B]">{{ $job->neutral_count }}</div>
                    <div class="text-[10px] text-[#555552]">Netral ({{ $job->neutral_percentage }}%)</div>
                </div>
                <div>
                    <div class="font-bold text-[#C62828]">{{ $job->negative_count }}</div>
                    <div class="text-[10px] text-[#555552]">Negatif ({{ $job->negative_percentage }}%)</div>
                </div>
            </div>
        </div>

        <!-- Verdict Card -->
        <div class="neu-card p-6 lg:col-span-2 flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#555552]">Kesimpulan Analisis</span>
                <div class="mt-2 flex items-center space-x-2">
                    <span class="text-xl font-bold text-[#1D1D1B]">Kecenderungan:</span>
                    @if($job->dominant_sentiment === 'positive')
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-[#2E7D32]/10 text-[#2E7D32] border border-[#2E7D32]/30">
                            Dominan Positif
                        </span>
                    @elseif($job->dominant_sentiment === 'negative')
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-[#C62828]/10 text-[#C62828] border border-[#C62828]/30">
                            Dominan Negatif
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-[#1D1D1B]/5 text-[#1D1D1B] border border-[#D5D5CA]">
                            Berimbang / Netral
                        </span>
                    @endif
                </div>

                <p class="mt-2.5 text-xs text-[#555552] leading-relaxed">
                    Total {{ $job->total_scraped }} data opini dianalisis dengan rata-rata polaritas {{ $job->avg_sentiment_score }} (skala -1.0 s/d +1.0).
                    @if($job->positive_count > $job->negative_count)
                        Mayoritas respon orang menunjukkan kepuasan dan apresiasi terhadap topik ini.
                    @elseif($job->negative_count > $job->positive_count)
                        Mayoritas respon orang menyoroti kritik dan keluhan.
                    @else
                        Persepsi publik terbagi secara merata.
                    @endif
                </p>
            </div>

            <div class="mt-4 pt-3 border-t border-[#E7E7DD]">
                <div class="h-2 w-full bg-[#E7E7DD] rounded overflow-hidden flex">
                    <div style="width: {{ $job->positive_percentage }}%" class="bg-[#2E7D32]"></div>
                    <div style="width: {{ $job->neutral_percentage }}%" class="bg-[#9E9E9E]"></div>
                    <div style="width: {{ $job->negative_percentage }}%" class="bg-[#C62828]"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedbacks List (Neumorphic Card with Real Image & Full Content) -->
    <div class="neu-card p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-[#E7E7DD] pb-3">
            <div>
                <h2 class="text-sm font-bold text-[#1D1D1B]">Daftar Liputan & Ulasan Publik</h2>
                <p class="text-xs text-[#555552]">Ditemukan {{ $feedbacks->total() }} data</p>
            </div>

            <!-- Simple Filter -->
            <form action="{{ route('scraper.show', $job->id) }}" method="GET" class="flex items-center space-x-2">
                <select name="sentiment" onchange="this.form.submit()" class="neu-input px-2.5 py-1 text-xs">
                    <option value="">Semua Sentimen</option>
                    <option value="positive" {{ request('sentiment') === 'positive' ? 'selected' : '' }}>Positif</option>
                    <option value="neutral" {{ request('sentiment') === 'neutral' ? 'selected' : '' }}>Netral</option>
                    <option value="negative" {{ request('sentiment') === 'negative' ? 'selected' : '' }}>Negatif</option>
                </select>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ulasan..." class="neu-input px-2.5 py-1 text-xs">
            </form>
        </div>

        <div class="space-y-4">
            @forelse($feedbacks as $item)
                <div class="p-4 rounded neu-panel">
                    <div class="flex flex-col sm:flex-row items-start gap-4">
                        <!-- Foto Thumbnail Asli (Jika Berita/Konten) atau Avatar Dummy (Jika Netizen) -->
                        <div class="w-full sm:w-32 h-32 sm:h-24 rounded overflow-hidden bg-[#E7E7DD] border border-[#D5D5CA] flex-shrink-0">
                            @if($item->display_thumbnail)
                                <img src="{{ $item->display_thumbnail }}" 
                                     alt="Media {{ $item->author_name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center font-bold text-xs text-[#555552] bg-[#E7E7DD]">
                                    {{ substr($item->author_name, 0, 2) }}
                                </div>
                            @endif
                        </div>

                        <!-- Body Konten -->
                        <div class="flex-grow w-full space-y-1.5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-[#1D1D1B]">{{ $item->author_name }}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#E7E7DD] border border-[#D5D5CA] text-[#555552] uppercase font-semibold">
                                        {{ ucfirst($item->platform) }}
                                    </span>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $item->sentiment_badge_class }}">
                                        {{ $item->sentiment_label_indo }} ({{ $item->sentiment_score }})
                                    </span>
                                    @if($item->source_url)
                                        <a href="{{ $item->source_url }}" target="_blank" rel="noopener" class="text-xs text-[#555552] hover:text-[#1D1D1B]" title="Buka tautan asli">
                                            Tautan Asli &rarr;
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Judul / Kutipan Ulasan -->
                            <p class="text-xs text-[#1D1D1B] leading-relaxed font-medium">
                                "{{ $item->content_raw }}"
                            </p>

                            <!-- Accordion Paragraf Berita Lengkap -->
                            @if(!empty($item->full_content))
                                <div class="pt-1.5">
                                    <button type="button" 
                                            onclick="const el = document.getElementById('full-art-{{ $item->id }}'); el.classList.toggle('hidden');" 
                                            class="text-[11px] font-semibold text-[#555552] hover:text-[#1D1D1B] underline">
                                        Baca Isi Paragraf Berita / Konten Lengkap
                                    </button>

                                    <div id="full-art-{{ $item->id }}" class="hidden mt-2 p-3 rounded bg-[#FFFFF7] border border-[#D5D5CA] text-xs text-[#1D1D1B] whitespace-pre-line leading-relaxed">
                                        {{ $item->full_content }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-xs text-[#555552]">
                    Tidak ada ulasan yang cocok.
                </div>
            @endforelse
        </div>

        <div class="pt-3">
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
                        backgroundColor: ['#2E7D32', '#757575', '#C62828'],
                        borderColor: '#FFFFF7',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    cutout: '72%'
                }
            });
        }
    });
</script>
@endpush
