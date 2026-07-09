@extends('layouts.admin')

@section('page-title', 'Log Notifikasi')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-base font-semibold text-[#334155]">Log Notifikasi</h3>
                <p class="text-xs text-[#94A3B8] mt-1">Seluruh notifikasi yang dikirimkan sistem kepada pengguna.</p>
            </div>
            <!-- Summary Badge -->
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-[#E0F2FE] text-[#0369A1] text-xs font-semibold">
                    Total: {{ $notifikasis->count() }} Notifikasi
                </span>
                @php $unreadCount = $notifikasis->where('is_read', false)->count(); @endphp
                @if($unreadCount > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 text-red-600 text-xs font-semibold border border-red-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    {{ $unreadCount }} Belum Dibaca
                </span>
                @endif
            </div>
        </div>

        <!-- Notifikasi List -->
        <div class="space-y-3">
            @forelse($notifikasis as $notif)
            <div class="bg-white rounded-xl border shadow-sm p-4 flex items-start gap-4 transition hover:shadow-md {{ !$notif->is_read ? 'border-[#38BDF8]/40 bg-[#F0F9FF]' : 'border-gray-100' }}">
                <!-- Icon Notifikasi -->
                <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center {{ !$notif->is_read ? 'bg-[#0EA5E9]/10' : 'bg-slate-100' }}">
                    <svg class="w-5 h-5 {{ !$notif->is_read ? 'text-[#0EA5E9]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <p class="text-sm font-semibold text-[#334155]">{{ $notif->judul }}</p>
                        @if(!$notif->is_read)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-[#0EA5E9] text-white text-[9px] font-bold uppercase tracking-wider">
                            Baru
                        </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ $notif->pesan }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        <!-- User Badge -->
                        <span class="inline-flex items-center gap-1.5 text-[10px] text-[#94A3B8]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $notif->user->name ?? 'Pengguna Tidak Dikenal' }}
                        </span>
                        <!-- Timestamp -->
                        <span class="inline-flex items-center gap-1.5 text-[10px] text-[#94A3B8]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <!-- Read status indicator -->
                <div class="flex-shrink-0">
                    @if(!$notif->is_read)
                        <span class="w-2.5 h-2.5 rounded-full bg-[#0EA5E9] block mt-1 animate-pulse"></span>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-200 block mt-1"></span>
                    @endif
                </div>
            </div>
            @empty
            <!-- Empty State -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm py-16 flex flex-col items-center justify-center gap-3">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-400">Belum ada notifikasi</p>
                <p class="text-xs text-slate-300">Notifikasi akan muncul ketika ada aksi peminjaman atau pengembalian.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination (jika dibutuhkan di masa depan) -->
        @if(method_exists($notifikasis, 'hasPages') && $notifikasis->hasPages())
        <div class="mt-4">{{ $notifikasis->links() }}</div>
        @endif
    </div>
@endsection
