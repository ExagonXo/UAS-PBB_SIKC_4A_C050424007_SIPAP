@extends('layouts.admin')

@section('page-title', 'Detail Peminjaman')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex items-center gap-3">
            <a href="{{ route('peminjaman.index') }}" class="p-2 text-[#94A3B8] hover:text-[#334155] hover:bg-slate-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <h3 class="text-base font-semibold text-[#334155]">Detail Peminjaman</h3>
                <p class="text-xs text-[#94A3B8] mt-1">ID #{{ $peminjaman->id }} · Diajukan {{ $peminjaman->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
            <!-- Status Badge -->
            @if($peminjaman->status === 'pending')
                <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-600 px-3 py-1.5 rounded-full text-xs font-semibold border border-amber-100">
                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span> Menunggu Konfirmasi
                </span>
            @elseif($peminjaman->status === 'disetujui')
                <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-full text-xs font-semibold border border-emerald-100">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span> Siap Diambil
                </span>
            @elseif($peminjaman->status === 'dipinjam')
                <span class="inline-flex items-center gap-1.5 bg-[#E0F2FE] text-[#0EA5E9] px-3 py-1.5 rounded-full text-xs font-semibold border border-sky-100">
                    <span class="w-1.5 h-1.5 bg-[#0EA5E9] rounded-full"></span> Sedang Dipinjam
                </span>
            @elseif($peminjaman->status === 'selesai')
                <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-500 px-3 py-1.5 rounded-full text-xs font-semibold">
                    Selesai
                </span>
            @elseif($peminjaman->status === 'ditolak')
                <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-500 px-3 py-1.5 rounded-full text-xs font-semibold border border-red-100">
                    Ditolak
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <!-- Kiri: Info Peminjam + Alat -->
            <div class="md:col-span-3 space-y-6">
                <!-- Info Peminjam -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h4 class="text-sm font-semibold text-[#334155] mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#0EA5E9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Data Peminjam
                    </h4>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#0EA5E9] to-[#0C4A6E] flex items-center justify-center text-white font-bold text-lg uppercase flex-shrink-0">
                            {{ substr($peminjaman->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-[#334155]">{{ $peminjaman->user->name ?? 'N/A' }}</p>
                            <p class="text-xs text-[#94A3B8] mt-0.5">{{ $peminjaman->user->email ?? '' }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold mt-1 {{ ($peminjaman->user->role ?? '') === 'dosen' ? 'bg-sky-50 text-sky-700' : 'bg-purple-50 text-purple-700' }}">
                                {{ ucfirst($peminjaman->user->role ?? 'mahasiswa') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Info Alat -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h4 class="text-sm font-semibold text-[#334155] mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#0EA5E9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Alat yang Dipinjam
                    </h4>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                            @if($peminjaman->alat && $peminjaman->alat->gambar)
                                <img src="{{ asset('storage/' . $peminjaman->alat->gambar) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-[#334155]">{{ $peminjaman->alat->nama_alat ?? 'N/A' }}</p>
                            <p class="text-xs text-[#94A3B8] mt-0.5">Merk: {{ $peminjaman->alat->merk ?? '-' }}</p>
                            <p class="text-xs text-[#94A3B8] mt-0.5">Stok: {{ $peminjaman->alat->stok ?? 0 }} Unit</p>
                        </div>
                    </div>
                    @if($peminjaman->alat && $peminjaman->alat->deskripsi)
                    <p class="text-xs text-slate-500 mt-4 leading-relaxed border-t border-slate-50 pt-4">
                        {{ $peminjaman->alat->deskripsi }}
                    </p>
                    @endif
                </div>

                <!-- Catatan Peminjam (jika ada) -->
                @if($peminjaman->catatan ?? false)
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
                    <h4 class="text-xs font-semibold text-amber-700 mb-2 uppercase tracking-wider">Catatan Peminjam</h4>
                    <p class="text-sm text-amber-900 leading-relaxed">{{ $peminjaman->catatan }}</p>
                </div>
                @endif
            </div>

            <!-- Kanan: Timeline + Actions -->
            <div class="md:col-span-2 space-y-6">
                <!-- Ringkasan Waktu -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h4 class="text-sm font-semibold text-[#334155] mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#0EA5E9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Jadwal Peminjaman
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-[#94A3B8]">Tanggal Pinjam</span>
                            <span class="text-sm font-semibold text-[#334155]">{{ $peminjaman->tgl_pinjam->format('d M Y') }}</span>
                        </div>
                        <div class="border-t border-dashed border-slate-100"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-[#94A3B8]">Rencana Kembali</span>
                            <span class="text-sm font-semibold text-[#334155]">{{ $peminjaman->tgl_kembali_rencana->format('d M Y') }}</span>
                        </div>

                        @if($peminjaman->tgl_kembali_aktual ?? false)
                        <div class="border-t border-dashed border-slate-100"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-[#94A3B8]">Dikembalikan</span>
                            <span class="text-sm font-semibold text-emerald-600">{{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_aktual)->format('d M Y') }}</span>
                        </div>
                        @endif

                        <!-- Durasi -->
                        <div class="border-t border-slate-100 pt-3 mt-2">
                            @php
                                $durasi = $peminjaman->tgl_pinjam->diffInDays($peminjaman->tgl_kembali_rencana);
                            @endphp
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-[#94A3B8]">Durasi Pinjam</span>
                                <span class="text-xs font-semibold bg-[#E0F2FE] text-[#0369A1] px-2 py-0.5 rounded-full">{{ $durasi }} Hari</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Actions (only for admin) -->
                @if(Auth::user()->role === 'admin')
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h4 class="text-sm font-semibold text-[#334155] mb-4">Tindakan Admin</h4>
                    <div class="space-y-3">
                        @if($peminjaman->status === 'pending')
                            <!-- Setujui -->
                            <form action="{{ route('peminjaman.approve', $peminjaman->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-[#0369A1] hover:bg-[#0C4A6E] text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition active:scale-95 hover:shadow-lg hover:shadow-sky-600/10 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Setujui Peminjaman
                                </button>
                            </form>
                        @endif

                        @if($peminjaman->status === 'disetujui')
                            <!-- Serahkan -->
                            <form action="{{ route('peminjaman.handover', $peminjaman->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition active:scale-95 hover:shadow-lg flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                    </svg>
                                    Serahkan ke Peminjam
                                </button>
                            </form>
                        @endif

                        @if(in_array($peminjaman->status, ['pending', 'disetujui']))
                            <!-- Tolak -->
                            <button type="button"
                                onclick="if(confirm('Tolak peminjaman ini?')) { document.getElementById('form-tolak-{{ $peminjaman->id }}').submit(); }"
                                class="w-full border border-red-200 text-red-500 hover:bg-red-50 text-sm font-semibold rounded-lg px-4 py-2.5 transition active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Tolak Peminjaman
                            </button>
                            <form id="form-tolak-{{ $peminjaman->id }}" action="{{ route('peminjaman.reject', $peminjaman->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('PATCH')
                            </form>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
