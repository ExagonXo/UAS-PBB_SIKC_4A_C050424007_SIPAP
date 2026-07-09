@php
    $user = Auth::user();

    $navigationItems = [
        [
            'label' => 'Dashboard',
            'route' => route('dashboard'),
            'active' => 'dashboard',
            'icon' => 'dashboard',
        ],
        [
            'label' => 'Daftar Alat',
            'route' => route('alat.index'),
            'active' => 'alat.index',
            'icon' => 'device',
        ],
        [
            'label' => $user->role === 'mahasiswa' ? 'Riwayat Pinjam' : 'Peminjaman',
            'route' => route('peminjaman.index'),
            'active' => 'peminjaman.index',
            'icon' => 'clock',
        ],
        [
            'label' => 'Pengembalian',
            'route' => route('pengembalian.index'),
            'active' => 'pengembalian.index',
            'icon' => 'return',
        ],
    ];

    if ($user->role !== 'mahasiswa') {
        $navigationItems[] = [
            'label' => 'Data Master',
            'route' => route('dashboard'),
            'active' => 'dashboard',
            'icon' => 'grid',
            'disabled' => true,
        ];
    }
@endphp

<aside class="fixed left-0 top-0 z-30 flex h-screen w-[280px] shrink-0 flex-col overflow-hidden border-r border-white/8 bg-[#071126] text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(124,58,237,0.16),_transparent_46%)]"></div>
    <div class="relative flex h-full flex-col">
        <div class="px-6 pt-8">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-[#7C3AED] via-[#4F46E5] to-[#22D3EE] shadow-[0_18px_35px_rgba(99,102,241,0.35)]">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6 text-white" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5h15M7.5 3.75h9A2.25 2.25 0 0 1 18.75 6v12A2.25 2.25 0 0 1 16.5 20.25h-9A2.25 2.25 0 0 1 5.25 18V6A2.25 2.25 0 0 1 7.5 3.75Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5h7.5M8.25 14.25h5.25" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <p class="text-[11px] uppercase tracking-[0.28em] text-slate-400">Sistem Kampus</p>
                    <h1 class="truncate text-lg font-semibold text-white">SIPAP Panel</h1>
                </div>
            </div>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-4 pb-6 pt-8">
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-500">
                    Navigasi Utama
                </p>

                <div class="mt-3 space-y-1.5">
                    @foreach($navigationItems as $item)
                        @if(!empty($item['disabled']))
                            <div class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-slate-500 opacity-80">
                                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/5 text-slate-400">
                                    @if($item['icon'] === 'grid')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5h6v6h-6zM13.5 4.5h6v6h-6zM4.5 13.5h6v6h-6zM13.5 13.5h6v6h-6z" />
                                        </svg>
                                    @endif
                                </span>
                                <span class="flex-1">{{ $item['label'] }}</span>
                            </div>
                            @continue
                        @endif

                        @php $isActive = request()->routeIs($item['active']); @endphp

                        <a
                            href="{{ $item['route'] }}"
                            class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-300 {{ $isActive ? 'bg-gradient-to-r from-[#6D28D9] to-[#8B5CF6] text-white shadow-[0_18px_35px_rgba(109,40,217,0.36)]' : 'text-slate-300 hover:-translate-y-0.5 hover:bg-white/8 hover:text-white' }}"
                        >
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl transition-colors duration-300 {{ $isActive ? 'bg-white/15 text-white' : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white' }}">
                                @if($item['icon'] === 'dashboard')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25h6v6h-6zM13.5 5.25h6v4.5h-6zM13.5 12.75h6v6h-6zM4.5 14.25h6v4.5h-6z" />
                                    </svg>
                                @elseif($item['icon'] === 'device')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 6.75h13.5a1.5 1.5 0 0 1 1.5 1.5v7.5a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-7.5a1.5 1.5 0 0 1 1.5-1.5Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 18v1.5M15 18v1.5" />
                                    </svg>
                                @elseif($item['icon'] === 'clock')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8">
                                        <circle cx="12" cy="12" r="8.25" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.75v4.5l3.25 2" />
                                    </svg>
                                @elseif($item['icon'] === 'return')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5h6v6h-6zM13.5 4.5h6v6h-6zM4.5 13.5h6v6h-6zM13.5 13.5h6v6h-6z" />
                                    </svg>
                                @endif
                            </span>

                            <span class="flex-1">{{ $item['label'] }}</span>

                            @if($isActive)
                                <span class="h-2 w-2 rounded-full bg-white"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>

        <div class="relative px-4 pb-5">
            <div class="rounded-[28px] border border-white/8 bg-white/5 p-4 backdrop-blur-sm">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-600 to-slate-800 text-sm font-semibold text-white ring-1 ring-white/10">
                            {{ substr($user->name, 0, 1) }}
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-white">{{ $user->name }}</p>
                            <p class="truncate text-[11px] uppercase tracking-[0.2em] text-slate-400">{{ $user->role }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl text-slate-300 transition-colors duration-300 hover:bg-white/10 hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7.5V6a1.5 1.5 0 0 1 1.5-1.5h6A1.5 1.5 0 0 1 18 6v12a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 9 18v-1.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12h7.5M16.5 9l3 3-3 3" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12H4.5" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</aside>
