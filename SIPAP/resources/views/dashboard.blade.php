@extends('layouts.admin')

@section('page-title', 'Dashboard Overview')

@section('content')
    <div class="space-y-6">
        <!-- KOMPONEN 1 — Banner Selamat Datang -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0369A1] via-[#0EA5E9] to-[#38BDF8] p-6 text-white shadow-sm">
            <div class="absolute -right-10 top-0 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -bottom-10 left-1/3 h-32 w-32 rounded-full bg-white/15 blur-2xl"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="space-y-2">
                    <h1 class="text-2xl font-bold tracking-tight">
                        Selamat Datang Kembali, {{ auth()->user()->name }}!
                    </h1>
                    <p class="text-sm text-blue-100 max-w-xl">
                        Kelola peminjaman alat presentasi kampus dari panel ini. Lacak stok alat, konfirmasi pengajuan, dan pantau pengembalian secara real-time.
                    </p>
                </div>
                <!-- Projector SVG Illustration -->
                <div class="hidden md:block shrink-0 opacity-20 hover:opacity-35 transition duration-300">
                    <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16v8H4V6zm2 2v4h12V8H6zM1 18h22v2H1v-2zm3-3h16v1H4v-1zm4 5h8v1H8v-1z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- KOMPONEN 2 — Grid 4 Statistik -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Stat Card 1 — Total Alat -->
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#38BDF8] p-5 flex items-center justify-between">
                <div class="space-y-1.5">
                    <p class="text-xs font-medium text-[#94A3B8] uppercase tracking-wide">Total Alat</p>
                    <h3 class="text-3xl font-bold text-[#0C4A6E]">{{ $totalAlat }}</h3>
                    <p class="text-[11px] text-[#94A3B8] font-medium">{{ $alatTersedia }} jenis tersedia</p>
                </div>
                <div class="bg-[#E0F2FE] text-[#0EA5E9] rounded-xl p-3 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>

            <!-- Stat Card 2 — Peminjaman Aktif -->
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#F59E0B] p-5 flex items-center justify-between">
                <div class="space-y-1.5">
                    <p class="text-xs font-medium text-[#94A3B8] uppercase tracking-wide">Dipinjam</p>
                    <h3 class="text-3xl font-bold text-[#0C4A6E]">{{ $peminjamanAktif }}</h3>
                    <p class="text-[11px] text-[#94A3B8] font-medium">Sedang aktif digunakan</p>
                </div>
                <div class="bg-amber-50 text-amber-500 rounded-xl p-3 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>

            <!-- Stat Card 3 — Menunggu Konfirmasi -->
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#EF4444] p-5 flex items-center justify-between">
                <div class="space-y-1.5">
                    <p class="text-xs font-medium text-[#94A3B8] uppercase tracking-wide">Perlu Konfirmasi</p>
                    <h3 class="text-3xl font-bold {{ $menungguKonfirmasi > 0 ? 'text-[#EF4444]' : 'text-[#0C4A6E]' }}">{{ $menungguKonfirmasi }}</h3>
                    <p class="text-[11px] text-[#94A3B8] font-medium">Pengajuan menunggu</p>
                </div>
                <div class="bg-red-50 text-red-400 rounded-xl p-3 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Stat Card 4 — Selesai Bulan Ini -->
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#10B981] p-5 flex items-center justify-between">
                <div class="space-y-1.5">
                    <p class="text-xs font-medium text-[#94A3B8] uppercase tracking-wide">Selesai Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-[#0C4A6E]">{{ $selesaiBulanIni }}</h3>
                    <p class="text-[11px] text-[#94A3B8] font-medium">Peminjaman terlunasi</p>
                </div>
                <div class="bg-emerald-50 text-emerald-500 rounded-xl p-3 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- KOMPONEN 3 — Dua Kolom -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Card Kiri — Pengajuan Peminjaman Terbaru -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white">
                    <h3 class="text-base font-semibold text-[#334155]">Pengajuan Peminjaman Terbaru</h3>
                    <a href="{{ route('peminjaman.index') }}" class="text-[#0EA5E9] hover:text-[#0369A1] text-xs font-semibold transition">Lihat Semua</a>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                        <thead class="bg-gray-50 text-[11px] font-semibold text-[#94A3B8] uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Peminjam</th>
                                <th class="px-6 py-3">Alat</th>
                                <th class="px-6 py-3">Tanggal Pinjam</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($upcomingPeminjamans as $item)
                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-[#334155]">{{ $item->user->name ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-[#94A3B8] tracking-wide">{{ ucfirst($item->user->role ?? 'mahasiswa') }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $item->alat->nama_alat ?? 'None' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ $item->tgl_pinjam->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->status === 'pending')
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-[#F59E0B]">
                                            Pending
                                        </span>
                                    @elseif($item->status === 'disetujui')
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-[#10B981]">
                                            Disetujui
                                        </span>
                                    @elseif($item->status === 'ditolak')
                                        <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-[#EF4444]">
                                            Ditolak
                                        </span>
                                    @elseif($item->status === 'selesai')
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <p class="text-xs">Belum ada pengajuan peminjaman.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card Kanan — Pengembalian Menunggu Konfirmasi (Peminjaman Aktif yang belum dikembalikan) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white">
                    <h3 class="text-base font-semibold text-[#334155]">Pengembalian Alat Menunggu</h3>
                    <a href="{{ route('pengembalian.index') }}" class="text-[#0EA5E9] hover:text-[#0369A1] text-xs font-semibold transition">Lihat Semua</a>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                        <thead class="bg-gray-50 text-[11px] font-semibold text-[#94A3B8] uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Peminjam</th>
                                <th class="px-6 py-3">Alat</th>
                                <th class="px-6 py-3">Tgl Kembali Rencana</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($pengembaliansPending as $item)
                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-[#334155]">{{ $item->user->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $item->alat->nama_alat ?? 'None' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ $item->tgl_kembali_rencana->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('pengembalian.index') }}" class="inline-flex items-center justify-center bg-[#0EA5E9] text-white text-xs font-medium rounded-lg px-3.5 py-1.5 hover:bg-[#0369A1] transition active:scale-95">
                                        Konfirmasi
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-xs">Tidak ada pengembalian alat yang tertunda.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
