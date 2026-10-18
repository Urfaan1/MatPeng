<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#123E34">
    <meta name="description" content="MatPeng — Aplikasi Manajemen Keuangan Pribadi yang cerdas, cepat, dan intuitif.">

    <title>@yield('title', 'Masuk') — MatPeng</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- PWA Manifest & Icons -->
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/svg+xml" href="/icons/icon-192.svg">
    <link rel="apple-touch-icon" href="/icons/icon-192.svg">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col justify-center py-10 sm:px-6 lg:px-8 bg-gradient-to-b from-[#F8FAFC] via-[#f0f6f4] to-[#F8FAFC] text-slate-800 antialiased selection:bg-emerald-800 selection:text-white">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        <!-- Logo -->
        <a href="/" class="inline-flex items-center gap-3 group">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-300 flex items-center justify-center shadow-lg shadow-emerald-950/20 group-hover:scale-105 transition-transform">
                <i data-lucide="wallet" class="w-6 h-6 text-[#123E34] stroke-[2.5]"></i>
            </div>
            <div class="text-left">
                <span class="text-2xl font-black tracking-tight text-[#123E34] block leading-none">MatPeng</span>
                <span class="text-[11px] font-medium text-slate-500">Manajemen Keuangan Pribadi</span>
            </div>
        </a>

        <h2 class="mt-6 text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">
            @yield('heading', 'Selamat Datang')
        </h2>
        <p class="mt-1 text-xs md:text-sm text-slate-500">
            @yield('subheading', 'Kelola arus kas pribadi Anda dengan mudah dan aman.')
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <!-- Flash Alert -->
        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-2.5 text-xs md:text-sm">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center gap-2.5 text-xs md:text-sm">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-slate-200/50 rounded-2xl border border-slate-200/80">
            @yield('content')
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} MatPeng. Lindungi privasi dan kebebasan finansial Anda.
        </p>
    </div>

    @stack('scripts')
</body>
</html>
