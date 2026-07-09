<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('page-title'); ?> | SIPAP Admin</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind & App Styles -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <style>
        body {
            font-family: 'Poppins', system-ui, -apple-system, sans-serif;
        }
        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #F8FAFC; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        /* Smooth transition for tab content */
        [x-show] { transition: opacity 0.15s ease; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#334155] antialiased">
    <div class="min-h-screen flex">
        <!-- SIDEBAR (Fixed, width 240px, bg #0C4A6E) -->
        <aside class="w-[240px] bg-[#0C4A6E] text-white fixed top-0 bottom-0 left-0 z-50 flex flex-col justify-between border-r border-[#0369A1]/30 shadow-lg">
            <div>
                <!-- Brand Logo / Header -->
                <div class="h-[60px] px-6 border-b border-[#0369A1]/30 flex items-center gap-3">
                    <!-- Icon Presentasi/Projector SVG -->
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#38BDF8] text-[#0C4A6E]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></svg>
                    </div>
                    <div>
                        <span class="font-bold tracking-wider text-white text-base">SIPALU</span>
                        <p class="text-[10px] text-slate-300 tracking-wide">Panel Admin</p>
                    </div>
                </div>

                <!-- Navigation List -->
                <nav class="p-4 space-y-1">
                    <!-- Dashboard -->
                    <?php $dashActive = request()->routeIs('dashboard'); ?>
                    <a href="<?php echo e(route('dashboard')); ?>" 
                       class="group flex items-center py-2.5 px-4 text-sm transition-all duration-200 <?php echo e($dashActive ? 'bg-[#0EA5E9]/20 text-white border-l-4 border-[#38BDF8] rounded-r-lg font-medium' : 'text-slate-300 hover:bg-white/10 hover:text-white rounded-lg'); ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo e($dashActive ? 'text-[#38BDF8]' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Alat -->
                    <?php $alatActive = request()->routeIs('alat.*'); ?>
                    <a href="<?php echo e(route('alat.index')); ?>" 
                       class="group flex items-center py-2.5 px-4 text-sm transition-all duration-200 <?php echo e($alatActive ? 'bg-[#0EA5E9]/20 text-white border-l-4 border-[#38BDF8] rounded-r-lg font-medium' : 'text-slate-300 hover:bg-white/10 hover:text-white rounded-lg'); ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo e($alatActive ? 'text-[#38BDF8]' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Daftar Alat
                    </a>

                    <!-- Peminjaman -->
                    <?php $pemActive = request()->routeIs('peminjaman.*'); ?>
                    <a href="<?php echo e(route('peminjaman.index')); ?>" 
                       class="group flex items-center justify-between py-2.5 px-4 text-sm transition-all duration-200 <?php echo e($pemActive ? 'bg-[#0EA5E9]/20 text-white border-l-4 border-[#38BDF8] rounded-r-lg font-medium' : 'text-slate-300 hover:bg-white/10 hover:text-white rounded-lg'); ?>">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 <?php echo e($pemActive ? 'text-[#38BDF8]' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Peminjaman
                        </div>
                        <?php $pendingCount = \App\Models\Peminjaman::where('status', 'pending')->count(); ?>
                        <?php if($pendingCount > 0): ?>
                            <span class="bg-[#EF4444] text-white text-[10px] font-bold px-2 py-0.5 rounded-full ring-2 ring-[#0C4A6E]">
                                <?php echo e($pendingCount); ?>

                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Pengembalian -->
                    <?php $kemActive = request()->routeIs('pengembalian.*'); ?>
                    <a href="<?php echo e(route('pengembalian.index')); ?>" 
                       class="group flex items-center py-2.5 px-4 text-sm transition-all duration-200 <?php echo e($kemActive ? 'bg-[#0EA5E9]/20 text-white border-l-4 border-[#38BDF8] rounded-r-lg font-medium' : 'text-slate-300 hover:bg-white/10 hover:text-white rounded-lg'); ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo e($kemActive ? 'text-[#38BDF8]' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Pengembalian
                    </a>

                    <!-- Pengguna (Placeholder route) -->
                    <?php $userActive = request()->is('admin/pengguna*'); ?>
                    <a href="/admin/pengguna" 
                       class="group flex items-center py-2.5 px-4 text-sm transition-all duration-200 <?php echo e($userActive ? 'bg-[#0EA5E9]/20 text-white border-l-4 border-[#38BDF8] rounded-r-lg font-medium' : 'text-slate-300 hover:bg-white/10 hover:text-white rounded-lg'); ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo e($userActive ? 'text-[#38BDF8]' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Pengguna
                    </a>

                    <!-- Log Notifikasi -->
                    <?php $notifActive = request()->is('admin/notifikasi*'); ?>
                    <a href="/admin/notifikasi" 
                       class="group flex items-center py-2.5 px-4 text-sm transition-all duration-200 <?php echo e($notifActive ? 'bg-[#0EA5E9]/20 text-white border-l-4 border-[#38BDF8] rounded-r-lg font-medium' : 'text-slate-300 hover:bg-white/10 hover:text-white rounded-lg'); ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo e($notifActive ? 'text-[#38BDF8]' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Log Notifikasi
                    </a>
                </nav>
            </div>

            <!-- Profile and Logout -->
            <div class="p-4 border-t border-[#0369A1]/30">
                <div class="flex items-center justify-between gap-3 p-2 bg-black/10 rounded-xl">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <!-- Initial Avatar -->
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#0EA5E9] text-white font-bold text-sm">
                            <?php echo e(substr(auth()->user()->name ?? 'A', 0, 1)); ?>

                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-semibold text-white"><?php echo e(auth()->user()->name); ?></p>
                            <p class="truncate text-[10px] text-slate-300 uppercase font-medium mt-0.5"><?php echo e(auth()->user()->role); ?></p>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="shrink-0">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="p-1.5 rounded-lg text-slate-300 hover:bg-white/10 hover:text-white transition-colors duration-150" title="Logout">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- WRAPPER FOR TOPBAR & MAIN CONTENT -->
        <div class="flex-1 flex flex-col pl-[240px]">
            <!-- TOPBAR (Fixed height 60px, bg white, border-bottom #F1F5F9, shadow-sm, z-index 40) -->
            <header class="h-[60px] bg-white border-b border-[#F1F5F9] shadow-sm flex items-center justify-between px-6 sticky top-0 z-40">
                <!-- Left Title -->
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-[#334155] truncate">
                        <?php echo $__env->yieldContent('page-title'); ?>
                    </h2>
                </div>

                <!-- Right items -->
                <div class="flex items-center gap-4">
                    <!-- Notification Bell -->
                    <?php $unreadNotifs = \App\Models\Notifikasi::where('is_read', false)->count(); ?>
                    <a href="/admin/notifikasi" class="relative p-1.5 text-[#94A3B8] hover:text-[#334155] transition-colors duration-150 rounded-lg hover:bg-[#F1F5F9]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <?php if($unreadNotifs > 0): ?>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-[#EF4444] rounded-full ring-2 ring-white"></span>
                        <?php endif; ?>
                    </a>

                    <!-- Vertical line divider -->
                    <div class="h-6 w-px bg-[#F1F5F9]"></div>

                    <!-- User Dropdown (click to open) -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <!-- Trigger -->
                        <button @click="open = !open" class="flex items-center gap-2.5 rounded-xl px-2.5 py-1.5 hover:bg-slate-50 transition cursor-pointer select-none">
                            <div class="hidden sm:block text-right">
                                <p class="text-xs font-semibold text-[#334155]"><?php echo e(auth()->user()->name); ?></p>
                                <p class="text-[10px] text-[#94A3B8] capitalize"><?php echo e(auth()->user()->role); ?></p>
                            </div>
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0EA5E9] text-white font-bold text-sm ring-2 ring-[#0EA5E9]/20 flex-shrink-0">
                                <?php echo e(substr(auth()->user()->name ?? 'A', 0, 1)); ?>

                            </div>
                            <!-- Chevron -->
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl shadow-slate-200/60 border border-slate-100 z-50 overflow-hidden"
                            x-cloak
                        >
                            <!-- User Info Header -->
                            <div class="px-4 py-3 bg-[#F8FAFC] border-b border-slate-100">
                                <p class="text-xs font-semibold text-[#334155] truncate"><?php echo e(auth()->user()->name); ?></p>
                                <p class="text-[10px] text-[#94A3B8] truncate mt-0.5"><?php echo e(auth()->user()->email); ?></p>
                                <span class="inline-flex mt-1.5 items-center px-2 py-0.5 rounded-md text-[9px] font-semibold uppercase tracking-wide bg-[#E0F2FE] text-[#0369A1]">
                                    <?php echo e(auth()->user()->role); ?>

                                </span>
                            </div>

                            <!-- Menu Items -->
                            <div class="p-1.5">
                                <a href="<?php echo e(route('profile.edit')); ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-[#334155] hover:bg-slate-50 transition group">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0EA5E9] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-xs font-medium">Profil Saya</span>
                                </a>
                            </div>

                            <!-- Divider -->
                            <div class="border-t border-slate-100 mx-2"></div>

                            <!-- Logout -->
                            <div class="p-1.5">
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-500 hover:bg-red-50 transition group">
                                        <svg class="w-4 h-4 text-red-400 group-hover:text-red-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span class="text-xs font-semibold">Keluar / Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-y-auto bg-[#F8FAFC]">
                <div class="p-6">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\UAS Pak Arifin\SIPAP\resources\views/layouts/admin.blade.php ENDPATH**/ ?>