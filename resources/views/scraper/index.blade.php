@extends('layouts.app', ['title' => 'Scraper Engine'])

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#1D1D1B]">Scraper Engine</h1>
            <p class="text-xs text-[#555552] mt-1">Tarik komentar dan opini publik dari platform pilihan dan jalankan analisis sentimen otomatis.</p>
        </div>
    </div>

    <!-- Scraper Form (Neumorphic Card) -->
    <div class="neu-card p-6 sm:p-8">
        <form id="scrapeForm" action="{{ route('scraper.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 1. Pilih Platform -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#1D1D1B] mb-3">1. Pilih Sumber / Platform</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <!-- Option All -->
                    <label class="cursor-pointer">
                        <input type="radio" name="platform" value="all" class="peer sr-only" checked>
                        <div class="p-3.5 rounded-md text-center bg-[#FFFFF7] border border-[#D5D5CA] neu-inset-peer peer-checked:bg-[#1D1D1B] peer-checked:text-[#FFFFF7] peer-checked:border-[#1D1D1B] hover:border-[#1D1D1B] transition shadow-sm">
                            <div class="text-xs font-semibold">Semua Platform</div>
                            <div class="text-[10px] opacity-75 mt-0.5">Multi-sumber</div>
                        </div>
                    </label>

                    <!-- Option News -->
                    <label class="cursor-pointer">
                        <input type="radio" name="platform" value="news" class="peer sr-only">
                        <div class="p-3.5 rounded-md text-center bg-[#FFFFF7] border border-[#D5D5CA] peer-checked:bg-[#1D1D1B] peer-checked:text-[#FFFFF7] peer-checked:border-[#1D1D1B] hover:border-[#1D1D1B] transition shadow-sm">
                            <div class="text-xs font-semibold">Portal Berita</div>
                            <div class="text-[10px] opacity-75 mt-0.5">Kompas, Detik, dll</div>
                        </div>
                    </label>

                    <!-- Option YouTube -->
                    <label class="cursor-pointer">
                        <input type="radio" name="platform" value="youtube" class="peer sr-only">
                        <div class="p-3.5 rounded-md text-center bg-[#FFFFF7] border border-[#D5D5CA] peer-checked:bg-[#1D1D1B] peer-checked:text-[#FFFFF7] peer-checked:border-[#1D1D1B] hover:border-[#1D1D1B] transition shadow-sm">
                            <div class="text-xs font-semibold">YouTube</div>
                            <div class="text-[10px] opacity-75 mt-0.5">Komentar Video</div>
                        </div>
                    </label>

                    <!-- Option Twitter -->
                    <label class="cursor-pointer">
                        <input type="radio" name="platform" value="twitter" class="peer sr-only">
                        <div class="p-3.5 rounded-md text-center bg-[#FFFFF7] border border-[#D5D5CA] peer-checked:bg-[#1D1D1B] peer-checked:text-[#FFFFF7] peer-checked:border-[#1D1D1B] hover:border-[#1D1D1B] transition shadow-sm">
                            <div class="text-xs font-semibold">Twitter / X</div>
                            <div class="text-[10px] opacity-75 mt-0.5">Tweet & Reaksi</div>
                        </div>
                    </label>

                    <!-- Option Google Reviews -->
                    <label class="cursor-pointer">
                        <input type="radio" name="platform" value="google_review" class="peer sr-only">
                        <div class="p-3.5 rounded-md text-center bg-[#FFFFF7] border border-[#D5D5CA] peer-checked:bg-[#1D1D1B] peer-checked:text-[#FFFFF7] peer-checked:border-[#1D1D1B] hover:border-[#1D1D1B] transition shadow-sm">
                            <div class="text-xs font-semibold">Google Review</div>
                            <div class="text-[10px] opacity-75 mt-0.5">Ulasan Publik</div>
                        </div>
                    </label>

                    <!-- Option Custom URL -->
                    <label class="cursor-pointer">
                        <input type="radio" name="platform" value="custom" class="peer sr-only">
                        <div class="p-3.5 rounded-md text-center bg-[#FFFFF7] border border-[#D5D5CA] peer-checked:bg-[#1D1D1B] peer-checked:text-[#FFFFF7] peer-checked:border-[#1D1D1B] hover:border-[#1D1D1B] transition shadow-sm">
                            <div class="text-xs font-semibold">Direct URL</div>
                            <div class="text-[10px] opacity-75 mt-0.5">Web Bebas</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 2. Input Query / URL & Limit -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-3">
                    <label for="queryInput" class="block text-xs font-bold uppercase tracking-wider text-[#1D1D1B] mb-2">
                        2. Kata Kunci / Topik / URL Target
                    </label>
                    <input type="text" id="queryInput" name="query" required
                           placeholder="Contoh: Layanan Paspor Imigrasi, Kebijakan Tarif PPN, iPhone 16 Pro, dll..."
                           class="neu-input w-full px-4 py-2.5 text-xs">
                    <p class="text-[11px] text-[#555552] mt-1.5">
                        Masukkan isu, nama produk, instansi, atau tautan video YouTube/URL web spesifik.
                    </p>
                </div>

                <div>
                    <label for="limitInput" class="block text-xs font-bold uppercase tracking-wider text-[#1D1D1B] mb-2">
                        3. Batas Data (Limit)
                    </label>
                    <select id="limitInput" name="limit" class="neu-input w-full px-4 py-2.5 text-xs">
                        <option value="10">10 Data Komentar</option>
                        <option value="20" selected>20 Data Komentar</option>
                        <option value="40">40 Data Komentar</option>
                        <option value="60">60 Data Komentar</option>
                    </select>
                    <p class="text-[11px] text-[#555552] mt-1.5">Jumlah ulasan yang diambil.</p>
                </div>
            </div>

            <!-- Options & Submit Button -->
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
                <label class="flex items-center space-x-2 text-xs text-[#555552] cursor-pointer">
                    <input type="checkbox" name="run_in_background" value="1" class="rounded border-[#D5D5CA] text-[#1D1D1B] focus:ring-0">
                    <span>Jalankan di Latar Belakang (Background Queue Worker)</span>
                </label>

                <button type="submit" id="submitBtn" class="neu-btn-primary px-6 py-2.5 text-xs font-medium flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Mulai Tarik & Analisis Data</span>
                </button>
            </div>
        </form>
    </div>

    <!-- History / Sesi Scrape Sebelumnya -->
    <div class="neu-card p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-[#D5D5CA] pb-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-[#1D1D1B]">Riwayat Sesi Scraping</h2>
            <span class="text-xs text-[#555552]">{{ $jobs->total() }} Sesi Tercatat</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-[#D5D5CA] text-[#555552] uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4">Topik / Query</th>
                        <th class="py-3 px-4">Platform</th>
                        <th class="py-3 px-4 text-center">Total</th>
                        <th class="py-3 px-4 text-center">Positif</th>
                        <th class="py-3 px-4 text-center">Netral</th>
                        <th class="py-3 px-4 text-center">Negatif</th>
                        <th class="py-3 px-4 text-center">Waktu</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E7E7DD] text-[#1D1D1B]">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-[#E7E7DD]/30 transition">
                            <td class="py-3.5 px-4 font-semibold text-[#1D1D1B] max-w-xs truncate">
                                <a href="{{ route('scraper.show', $job->id) }}" class="hover:underline">
                                    {{ $job->target_query }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[11px] bg-[#E7E7DD] border border-[#D5D5CA] text-[#1D1D1B]">
                                    {{ ucfirst($job->platform) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold">{{ $job->total_scraped }}</td>
                            <td class="py-3.5 px-4 text-center text-[#2E7D32] font-semibold">{{ $job->positive_count }}</td>
                            <td class="py-3.5 px-4 text-center text-[#555552] font-semibold">{{ $job->neutral_count }}</td>
                            <td class="py-3.5 px-4 text-center text-[#C62828] font-semibold">{{ $job->negative_count }}</td>
                            <td class="py-3.5 px-4 text-center text-xs text-[#555552]">
                                {{ $job->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('scraper.show', $job->id) }}" class="p-1.5 rounded text-[#555552] hover:text-[#1D1D1B] hover:bg-[#E7E7DD] transition" title="Lihat Laporan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('scraper.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Hapus sesi ini beserta datanya?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded text-[#C62828] hover:bg-[#FFCDD2]/30 transition" title="Hapus Sesi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-xs text-[#555552]">
                                Belum ada riwayat scraping tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('scrapeForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Sedang Menarik & Menganalisis Data...</span>
        `;
    });
</script>
@endpush
