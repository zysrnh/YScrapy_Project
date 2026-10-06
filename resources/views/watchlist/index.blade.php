@extends('layouts.app', ['title' => 'Target Pantauan Otomatis (Watchlist)'])

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#1D1D1B]">Target Pantauan Otomatis (Watchlist)</h1>
            <p class="text-xs text-[#555552] mt-1">
                Daftarkan kata kunci atau brand yang ingin di-scrape dan dipantau sentimennya secara berkala di latar belakang.
            </p>
        </div>
    </div>

    <!-- Info Box: Cara Kerja Scheduler -->
    <div class="neu-card p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-start space-x-3.5">
            <div class="px-3 py-2 rounded-xl neu-inset text-[#1D1D1B] text-xs font-bold uppercase tracking-wider">
                Scheduler
            </div>
            <div>
                <h2 class="text-sm font-bold text-[#1D1D1B]">Sistem Scheduler Otomatis Aktif</h2>
                <p class="text-xs text-[#555552] mt-0.5">
                    Target yang aktif akan di-scrape otomatis sesuai frekuensi yang ditentukan. Kamu juga bisa mengeksekusi worker langsung via terminal dengan:
                    <code class="px-2.5 py-1 rounded-lg neu-inset text-[#1D1D1B] font-mono text-[11px] ml-1">php artisan yscrapy:run-scheduled-monitoring</code>
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah Target Pantauan -->
        <div class="neu-card p-6 sm:p-7">
            <h2 class="text-sm font-bold uppercase tracking-wider text-[#1D1D1B] mb-4">Tambah Target Pantauan</h2>

            <form action="{{ route('watchlist.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1D1D1B] mb-2">Kata Kunci / Topik</label>
                    <input type="text" name="keyword" required placeholder="Contoh: Layanan Paspor, Nama Brand, dll..."
                           class="neu-input w-full px-3.5 py-2.5 text-xs placeholder:text-[#555552]/70">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1D1D1B] mb-2">Platform Target</label>
                    <select name="platform" class="neu-input w-full px-3.5 py-2.5 text-xs">
                        <option value="all">Semua Platform (Multi-Sumber)</option>
                        <option value="news">Portal Berita</option>
                        <option value="youtube">YouTube (Komentar)</option>
                        <option value="twitter">Twitter / X</option>
                        <option value="google_review">Google Reviews</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1D1D1B] mb-2">Frekuensi Scraping</label>
                    <select name="frequency" class="neu-input w-full px-3.5 py-2.5 text-xs">
                        <option value="hourly">Setiap 1 Jam</option>
                        <option value="every_six_hours">Setiap 6 Jam</option>
                        <option value="daily">Sekali Sehari</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1D1D1B] mb-2">Limit Data Per Siklus</label>
                    <select name="limit_per_run" class="neu-input w-full px-3.5 py-2.5 text-xs">
                        <option value="10">10 Ulasan</option>
                        <option value="20" selected>20 Ulasan</option>
                        <option value="30">30 Ulasan</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" class="neu-btn-primary w-full py-2.5 px-4 text-xs font-medium flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Simpan ke Watchlist</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Target Pantauan -->
        <div class="neu-card p-6 sm:p-7 lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#c5c5ba]/40">
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#1D1D1B]">Daftar Target Aktif</h2>
                <span class="text-xs text-[#555552]">{{ $watchlists->total() }} Target Terdaftar</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="text-[#555552] uppercase tracking-wider text-[11px]">
                            <th class="py-2.5 px-3">Topik</th>
                            <th class="py-2.5 px-3">Platform</th>
                            <th class="py-2.5 px-3">Frekuensi</th>
                            <th class="py-2.5 px-3 text-center">Status</th>
                            <th class="py-2.5 px-3">Terakhir Scraping</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-[#1D1D1B]">
                        @forelse($watchlists as $item)
                            <tr class="hover:bg-[#dfdfd4]/50 transition rounded-xl">
                                <td class="py-3.5 px-3 font-semibold text-[#1D1D1B]">
                                    {{ $item->keyword }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] neu-inset text-[#1D1D1B] uppercase font-semibold">
                                        {{ $item->platform }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-[#555552]">
                                    {{ $item->frequency_label }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <form action="{{ route('watchlist.toggle', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-semibold transition {{ $item->is_active ? 'neu-inset text-[#2E7D32]' : 'neu-btn-secondary text-[#555552]' }}">
                                            {{ $item->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3.5 px-3 text-[#555552]">
                                    {{ $item->last_run_at ? $item->last_run_at->diffForHumans() : 'Belum pernah' }}
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <!-- Tombol Jalankan Sekarang -->
                                        <form action="{{ route('watchlist.run', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="neu-btn-secondary px-3 py-1.5 text-[11px]" title="Jalankan Scraping Sekarang">
                                                Jalankan
                                            </button>
                                        </form>

                                        <!-- Hapus -->
                                        <form action="{{ route('watchlist.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus target pantauan ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg neu-btn-secondary text-[#C62828] hover:bg-[#FFCDD2]/30 transition" title="Hapus">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-xs text-[#555552]">
                                    Belum ada target di Watchlist. Tambahkan target di form sebelah kiri!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $watchlists->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
