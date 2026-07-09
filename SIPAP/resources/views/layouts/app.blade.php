<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#F7F9FC] text-slate-900">
        <div class="min-h-screen overflow-x-hidden bg-[#F7F9FC]">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex min-h-screen min-w-0 flex-col pl-[280px]">
                <!-- Top Header -->
                <header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/85 backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-6 px-6 py-4 sm:px-8 lg:px-10">
                        <div class="min-w-0 flex-1">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>

                        <div class="flex items-center gap-4">
                            <button class="relative inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 shadow-sm transition-colors duration-300 hover:bg-slate-50 hover:text-slate-900">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="absolute right-3 top-3 block h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                            </button>

                            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <div class="text-right">
                                    <p class="text-xs font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-[#081B4B] to-[#7C3AED] text-sm font-bold text-white shadow-md shadow-violet-500/20">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-[#F7F9FC]">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
