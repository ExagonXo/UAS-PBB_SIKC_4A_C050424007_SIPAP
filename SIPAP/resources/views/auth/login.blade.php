<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login · SIPAP Admin</title>

    <!-- Poppins Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Poppins', system-ui, sans-serif; }

        /* Animated gradient background */
        .bg-gradient-auth {
            background: linear-gradient(135deg, #0C4A6E 0%, #0369A1 40%, #0EA5E9 80%, #38BDF8 100%);
            background-size: 300% 300%;
            animation: gradientShift 8s ease infinite;
        }
        @keyframes gradientShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glassmorphism card */
        .glass-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 9999px;
            filter: blur(60px);
            opacity: 0.25;
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-20px); }
        }

        /* Input focus ring */
        .input-field:focus {
            outline: none;
            border-color: #0EA5E9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.12);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-auth relative overflow-hidden">

    <!-- Decorative floating orbs -->
    <div class="orb w-72 h-72 bg-white top-[-80px] left-[-80px]" style="animation-delay: 0s;"></div>
    <div class="orb w-96 h-96 bg-[#38BDF8] bottom-[-100px] right-[-100px]" style="animation-delay: 2s;"></div>
    <div class="orb w-48 h-48 bg-white top-1/2 left-10" style="animation-delay: 4s;"></div>

    <!-- Grid pattern overlay -->
    <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.8) 1px, transparent 0); background-size: 30px 30px;"></div>

    <!-- Login Card -->
    <div class="glass-card rounded-2xl shadow-2xl shadow-[#0C4A6E]/30 w-full max-w-md mx-4 relative z-10">

        <!-- Card Header -->
        <div class="px-8 pt-8 pb-6 text-center border-b border-slate-100">
            <!-- Logo / Brand -->
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-[#0369A1] to-[#0C4A6E] shadow-lg shadow-[#0369A1]/30 mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-[#0C4A6E] tracking-tight">SIPAP</h1>
            <p class="text-xs text-slate-400 mt-1 tracking-wide">Sistem Informasi Peminjaman Alat Presentasi</p>
        </div>

        <!-- Form Body -->
        <div class="px-8 py-7">
            <p class="text-sm font-semibold text-[#334155] mb-5">Masuk ke Panel Admin</p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 bg-emerald-50 text-emerald-700 text-xs font-medium px-4 py-3 rounded-lg border border-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 text-red-600 text-xs font-medium px-4 py-3 rounded-lg border border-red-100">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-[#334155] mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="admin@sipap.ac.id"
                            class="input-field w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-[#334155] placeholder-slate-300 bg-slate-50/50 transition @error('email') border-red-400 bg-red-50 @enderror"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div x-data="{ showPass: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-[#334155]">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[10px] text-[#0EA5E9] hover:text-[#0369A1] font-medium transition">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input
                            id="password"
                            name="password"
                            :type="showPass ? 'text' : 'password'"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="input-field w-full pl-9 pr-10 py-2.5 rounded-xl border border-gray-200 text-sm text-[#334155] placeholder-slate-300 bg-slate-50/50 transition @error('password') border-red-400 bg-red-50 @enderror"
                        >
                        <!-- Toggle password visibility -->
                        <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-2">
                    <input
                        id="remember_me"
                        name="remember"
                        type="checkbox"
                        class="w-3.5 h-3.5 rounded border-gray-300 text-[#0369A1] focus:ring-[#38BDF8] focus:ring-offset-0"
                    >
                    <label for="remember_me" class="text-xs text-slate-500 select-none cursor-pointer">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-[#0369A1] to-[#0C4A6E] hover:from-[#0C4A6E] hover:to-[#0C4A6E] text-white font-semibold text-sm py-3 rounded-xl shadow-lg shadow-[#0369A1]/30 hover:shadow-xl hover:shadow-[#0369A1]/40 transition-all duration-200 active:scale-[0.98] mt-2"
                >
                    Masuk ke Dashboard
                </button>
            </form>
        </div>

        <!-- Card Footer -->
        <div class="px-8 pb-7 text-center">
            <p class="text-[10px] text-slate-300">
                SIPAP v1.0 · Panel Admin Kampus
            </p>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
