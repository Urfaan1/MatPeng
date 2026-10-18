<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#123E34">
    <meta name="description" content="MatPeng - Aplikasi Manajemen Keuangan Pribadi yang cerdas, cepat, dan intuitif.">
    
    <title>@yield('title', 'Dashboard') — MatPeng</title>

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
<body class="h-full text-slate-800 antialiased selection:bg-emerald-800 selection:text-white flex flex-col md:flex-row bg-[#F8FAFC]">

    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 z-30 bg-[#123E34] text-white shadow-xl">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 h-20 border-b border-emerald-900/60">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-400 to-teal-200 flex items-center justify-center shadow-md">
                <i data-lucide="wallet" class="w-5 h-5 text-[#123E34] stroke-[2.5]"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-white flex items-center gap-1.5">
                    MatPeng
                    <span class="text-[10px] font-semibold bg-emerald-700/80 text-emerald-100 px-1.5 py-0.5 rounded-full">v1.0</span>
                </h1>
                <p class="text-[11px] text-emerald-300/80">Manajemen Keuangan</p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-[#1B594B] text-white shadow-sm font-semibold' : 'text-emerald-100/80 hover:bg-emerald-900/40 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                Dashboard
            </a>

            <a href="{{ route('transactions.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('transactions.*') ? 'bg-[#1B594B] text-white shadow-sm font-semibold' : 'text-emerald-100/80 hover:bg-emerald-900/40 hover:text-white' }}">
                <i data-lucide="arrow-left-right" class="w-5 h-5"></i>
                Transaksi
            </a>

            <a href="{{ route('categories.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('categories.*') ? 'bg-[#1B594B] text-white shadow-sm font-semibold' : 'text-emerald-100/80 hover:bg-emerald-900/40 hover:text-white' }}">
                <i data-lucide="tags" class="w-5 h-5"></i>
                Kategori
            </a>

            <a href="{{ route('reports.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('reports.*') ? 'bg-[#1B594B] text-white shadow-sm font-semibold' : 'text-emerald-100/80 hover:bg-emerald-900/40 hover:text-white' }}">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                Laporan
            </a>

            <a href="{{ route('settings.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('settings.*') ? 'bg-[#1B594B] text-white shadow-sm font-semibold' : 'text-emerald-100/80 hover:bg-emerald-900/40 hover:text-white' }}">
                <i data-lucide="settings" class="w-5 h-5"></i>
                Pengaturan
            </a>
        </nav>

        <!-- Sidebar User & Quick CTA -->
        <div class="p-4 border-t border-emerald-900/60 bg-[#0d2c25]/40">
            <a href="{{ route('transactions.create') }}" class="w-full mb-3 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-[#123E34] font-semibold text-sm transition-all shadow-md active:scale-[0.98]">
                <i data-lucide="plus-circle" class="w-4 h-4 stroke-[2.5]"></i>
                Tambah Transaksi
            </a>

            <div class="flex items-center justify-between gap-3 pt-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-emerald-700 flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ substr(Auth::user()->name ?? 'P', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Pengguna' }}</p>
                        <p class="text-[11px] text-emerald-300/70 truncate">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" title="Keluar dari akun" class="p-2 rounded-xl text-emerald-300 hover:text-white hover:bg-emerald-900/60 transition-colors">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col md:pl-64 min-h-screen">
        <!-- Mobile Header -->
        <header class="md:hidden sticky top-0 z-20 bg-[#123E34] text-white px-4 py-3.5 flex items-center justify-between shadow-md">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-400 to-teal-200 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-4 h-4 text-[#123E34] stroke-[2.5]"></i>
                </div>
                <span class="font-bold text-lg tracking-tight text-white">MatPeng</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs bg-emerald-800/80 text-emerald-200 px-2.5 py-1 rounded-full border border-emerald-700/60">
                    {{ now()->locale('id')->isoFormat('D MMM Y') }}
                </span>
                <a href="{{ route('settings.index') }}" class="w-8 h-8 rounded-full bg-emerald-700 flex items-center justify-center text-white text-xs font-bold">
                    {{ substr(Auth::user()->name ?? 'P', 0, 1) }}
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mx-4 md:mx-8 mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 text-sm">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-4 md:mx-8 mt-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center gap-3 text-sm">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Body -->
        <main class="flex-1 px-4 py-5 md:px-8 md:py-8 pb-28 md:pb-12 max-w-7xl w-full mx-auto">
            @yield('content')
        </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-2xl px-2 py-1.5 flex items-center justify-around">
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl text-xs {{ request()->routeIs('dashboard') ? 'text-[#123E34] font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'stroke-[2.5]' : '' }}"></i>
            <span>Beranda</span>
        </a>

        <a href="{{ route('transactions.index') }}" 
           class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl text-xs {{ request()->routeIs('transactions.*') && !request()->routeIs('transactions.create') ? 'text-[#123E34] font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
            <i data-lucide="arrow-left-right" class="w-5 h-5"></i>
            <span>Transaksi</span>
        </a>

        <!-- Floating Add Button in Middle -->
        <a href="{{ route('transactions.create') }}" 
           class="-mt-5 w-12 h-12 rounded-full bg-[#123E34] hover:bg-[#1B594B] text-white flex items-center justify-center shadow-lg shadow-emerald-900/30 transition-transform active:scale-95 border-2 border-white">
            <i data-lucide="plus" class="w-6 h-6 stroke-[2.5]"></i>
        </a>

        <a href="{{ route('reports.index') }}" 
           class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl text-xs {{ request()->routeIs('reports.*') ? 'text-[#123E34] font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            <span>Laporan</span>
        </a>

        <a href="{{ route('categories.index') }}" 
           class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl text-xs {{ request()->routeIs('categories.*') ? 'text-[#123E34] font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
            <i data-lucide="tags" class="w-5 h-5"></i>
            <span>Kategori</span>
        </a>
    </nav>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('MatPeng PWA Service Worker terdaftar'))
                    .catch((err) => console.warn('PWA SW error:', err));
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
