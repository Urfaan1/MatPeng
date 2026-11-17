@extends('layouts.app')

@section('title', 'Panel Admin')

@section('content')
<div class="space-y-6">
    <!-- Header Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-[#123E34] to-[#1B594B] p-6 rounded-2xl text-white shadow-lg">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-400 text-slate-900 shadow-sm">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    Area Khusus Admin
                </span>
            </div>
            <h2 class="text-2xl font-black tracking-tight mt-2">Ringkasan Sistem & Pengguna</h2>
            <p class="text-xs sm:text-sm text-emerald-200/80 mt-1">Pantau seluruh pengguna, aktivitas transaksi, dan volume keuangan aplikasi MatPeng.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs sm:text-sm font-semibold transition-all backdrop-blur-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Ke Dashboard Pribadi
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Total Users -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalUsers) }}</h3>
                <p class="text-[11px] text-amber-600 font-medium mt-1">Termasuk {{ $totalAdmins }} Administrator</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Transaksi</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalTransactions) }}</h3>
                <p class="text-[11px] text-emerald-600 font-medium mt-1">Dari seluruh pengguna aktif</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="receipt" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Volume Pemasukan Global -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Volume Pemasukan</p>
                <h3 class="text-xl font-black text-emerald-700 mt-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-400 mt-1">Akumulasi global</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Volume Pengeluaran Global -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Volume Pengeluaran</p>
                <h3 class="text-xl font-black text-rose-600 mt-1">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-400 mt-1">Akumulasi global</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i data-lucide="trending-down" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Card Header & Filter -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Daftar Pengguna Terdaftar</h3>
                <p class="text-xs text-slate-500">Kelola pengguna dan kontrol hak akses administrator</p>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="relative w-full sm:w-64">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama atau email..." 
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#123E34]/20 focus:border-[#123E34]">
                </div>
                <button type="submit" class="px-3 py-2 bg-[#123E34] hover:bg-[#1B594B] text-white text-xs font-semibold rounded-xl transition-all shadow-sm">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.dashboard') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100" title="Reset Pencarian">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Status / Hak Akses</th>
                        <th class="py-3 px-4 text-center">Transaksi</th>
                        <th class="py-3 px-4 text-center">Kategori</th>
                        <th class="py-3 px-4">Terdaftar Sejak</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full {{ $user->is_admin ? 'bg-amber-500 text-slate-950 font-black' : 'bg-emerald-700 text-white font-bold' }} flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900 text-sm flex items-center gap-1.5">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="text-[10px] bg-slate-100 text-slate-600 font-medium px-1.5 py-0.5 rounded">Anda</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if($user->is_admin)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-amber-700"></i>
                                        Administrator
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                        <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                                        Pengguna Biasa
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-semibold text-slate-700">
                                {{ $user->transactions_count }}
                            </td>
                            <td class="py-3 px-4 text-center text-slate-500">
                                {{ $user->categories_count }}
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status hak akses pengguna ini?');">
                                        @csrf
                                        @if($user->is_admin)
                                            <button type="submit" class="px-2.5 py-1 text-xs font-medium text-rose-700 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 rounded-lg transition-colors">
                                                Cabut Admin
                                            </button>
                                        @else
                                            <button type="submit" class="px-2.5 py-1 text-xs font-medium text-emerald-800 hover:text-white bg-emerald-50 hover:bg-emerald-600 border border-emerald-200 rounded-lg transition-colors">
                                                Jadikan Admin
                                            </button>
                                        @endif
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 italic">Akun Anda</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-slate-400">
                                Tidak ada data pengguna yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
