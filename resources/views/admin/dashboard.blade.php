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
                    Panel Administrator
                </span>
                @if($pendingUsersCount > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500 text-white animate-pulse">
                        {{ $pendingUsersCount }} Butuh Konfirmasi
                    </span>
                @endif
            </div>
            <h2 class="text-2xl font-black tracking-tight mt-2">Ringkasan Sistem & Manajemen Pengguna</h2>
            <p class="text-xs sm:text-sm text-emerald-200/80 mt-1">Konfirmasi pendaftaran pengguna baru, pantau aktivitas transaksi, dan kelola akun pengguna.</p>
        </div>

        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-rose-600/80 text-white text-xs sm:text-sm font-semibold transition-all backdrop-blur-sm">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Keluar Admin
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Notifikasi Pengguna Pending -->
    @if($pendingUsersCount > 0)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-amber-900">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                    <i data-lucide="user-check" class="w-5 h-5 text-amber-700"></i>
                </div>
                <div>
                    <p class="text-sm font-bold">Ada {{ $pendingUsersCount }} pengguna baru yang menunggu persetujuan!</p>
                    <p class="text-xs text-amber-700">Pengguna belum dapat masuk ke MatPeng sebelum Anda mengaktifkan akun mereka.</p>
                </div>
            </div>
            <a href="{{ route('admin.dashboard', ['filter' => 'pending']) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all shrink-0">
                Lihat Permintaan
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    @endif

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Total Users -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalUsers) }}</h3>
                <p class="text-[11px] text-emerald-600 font-medium mt-1">{{ $activeUsersCount }} Aktif &bull; {{ $totalAdmins }} Admin</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Perlu Aktivasi</p>
                <h3 class="text-2xl font-black {{ $pendingUsersCount > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-1">
                    {{ number_format($pendingUsersCount) }}
                </h3>
                <p class="text-[11px] text-slate-400 mt-1">Menunggu persetujuan</p>
            </div>
            <div class="w-12 h-12 rounded-xl {{ $pendingUsersCount > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center shrink-0">
                <i data-lucide="user-plus" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Transaksi</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalTransactions) }}</h3>
                <p class="text-[11px] text-slate-400 mt-1">Akumulasi global</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="receipt" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Volume Keuangan Global -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemasukan / Pengeluaran</p>
                <p class="text-xs font-bold text-emerald-600 mt-1">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                <p class="text-xs font-bold text-rose-600">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center shrink-0">
                <i data-lucide="arrow-left-right" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Users Management Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Card Header & Filter -->
        <div class="p-5 border-b border-slate-100 flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Manajemen Pengguna</h3>
                    <p class="text-xs text-slate-500">Konfirmasi akun pendaftar baru dan kelola hak akses</p>
                </div>

                <!-- Search Form -->
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <input type="hidden" name="filter" value="{{ $filter }}">
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
                        <a href="{{ route('admin.dashboard', ['filter' => $filter]) }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100" title="Reset Pencarian">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Filter Tabs -->
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 text-xs">
                <a href="{{ route('admin.dashboard', ['search' => request('search'), 'filter' => 'all']) }}" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ $filter === 'all' ? 'bg-[#123E34] text-white font-semibold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua ({{ $totalUsers }})
                </a>
                <a href="{{ route('admin.dashboard', ['search' => request('search'), 'filter' => 'pending']) }}" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-1.5 {{ $filter === 'pending' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-amber-50 text-amber-900 border border-amber-200 hover:bg-amber-100' }}">
                    <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                    Menunggu Konfirmasi ({{ $pendingUsersCount }})
                </a>
                <a href="{{ route('admin.dashboard', ['search' => request('search'), 'filter' => 'active']) }}" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ $filter === 'active' ? 'bg-[#123E34] text-white font-semibold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Pengguna Aktif ({{ $activeUsersCount }})
                </a>
                <a href="{{ route('admin.dashboard', ['search' => request('search'), 'filter' => 'admin']) }}" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ $filter === 'admin' ? 'bg-[#123E34] text-white font-semibold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Administrator ({{ $totalAdmins }})
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Status Akun</th>
                        <th class="py-3 px-4">Hak Akses</th>
                        <th class="py-3 px-4 text-center">Transaksi</th>
                        <th class="py-3 px-4">Terdaftar</th>
                        <th class="py-3 px-4 text-right">Aksi Konfirmasi & Kelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ !$user->is_active && !$user->is_admin ? 'bg-amber-50/30' : '' }}">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full {{ $user->is_admin ? 'bg-amber-500 text-slate-950 font-black' : ($user->is_active ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-300 text-slate-700 font-bold') }} flex items-center justify-center text-xs shrink-0">
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
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <i data-lucide="check" class="w-3 h-3"></i> Aktif (Admin)
                                    </span>
                                @elseif($user->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <i data-lucide="check" class="w-3 h-3"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        <i data-lucide="clock" class="w-3 h-3 text-amber-700"></i> Menunggu Konfirmasi
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($user->is_admin)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-amber-700"></i>
                                        Administrator
                                    </span>
                                @else
                                    <span class="text-xs text-slate-500 font-medium">Pengguna Biasa</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-semibold text-slate-700">
                                {{ $user->transactions_count }}
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if($user->id !== auth()->id())
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Tombol Konfirmasi / Aktivasi -->
                                        @if(!$user->is_admin)
                                            <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="inline">
                                                @csrf
                                                @if(!$user->is_active)
                                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition-all flex items-center gap-1">
                                                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                                        Aktifkan Akun
                                                    </button>
                                                @else
                                                    <button type="submit" onclick="return confirm('Nonaktifkan akun ini? Pengguna tidak akan bisa login.');" class="px-2.5 py-1 text-xs font-medium text-slate-600 hover:text-red-700 hover:bg-red-50 border border-slate-200 rounded-lg transition-colors">
                                                        Nonaktifkan
                                                    </button>
                                                @endif
                                            </form>
                                        @endif

                                        <!-- Tombol Hak Akses Admin -->
                                        <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status hak akses pengguna ini?');">
                                            @csrf
                                            @if($user->is_admin)
                                                <button type="submit" title="Ubah menjadi user biasa" class="px-2 py-1 text-xs font-medium text-rose-700 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 rounded-lg transition-colors">
                                                    Cabut Admin
                                                </button>
                                            @else
                                                <button type="submit" title="Jadikan administrator" class="p-1.5 text-xs font-medium text-amber-700 hover:bg-amber-100 border border-amber-200 rounded-lg transition-colors">
                                                    <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Akun Anda</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-slate-400">
                                Tidak ada data pengguna yang sesuai kriteria.
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
