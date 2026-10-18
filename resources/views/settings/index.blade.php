@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pengaturan Akun</h2>
        <p class="text-sm text-slate-500">Kelola profil pribadi dan keamanan akun MatPeng Anda</p>
    </div>

    <!-- Info Akun Card -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#123E34] to-[#1B594B] text-white flex items-center justify-center font-bold text-xl shadow-md">
                {{ substr(Auth::user()->name ?? 'Pengguna', 0, 1) }}
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-lg">{{ Auth::user()->name ?? 'Tamu MatPeng' }}</h3>
                <p class="text-xs text-slate-500">{{ Auth::user()->email ?? 'tamu@matpeng.id' }}</p>
                <span class="inline-flex items-center gap-1 mt-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Akun Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- Form Profil -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Perbarui Profil</h3>
        <form method="POST" action="{{ route('settings.profile') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name ?? 'Pengguna') }}" required
                       class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', Auth::user()->email ?? 'demo@matpeng.id') }}" disabled
                       class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 bg-slate-50 text-slate-400 cursor-not-allowed">
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#123E34] hover:bg-[#1B594B] text-white text-xs md:text-sm font-semibold transition-all">
                Simpan Profil
            </button>
        </form>
    </div>

    <!-- Form Ubah Password -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Ubah Kata Sandi</h3>
        <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Password Saat Ini</label>
                <input type="password" name="current_password" required
                       class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Password Baru</label>
                <input type="password" name="new_password" required
                       class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34]">
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#123E34] hover:bg-[#1B594B] text-white text-xs md:text-sm font-semibold transition-all">
    <!-- Keluar Akun -->
    <div class="p-6 rounded-2xl bg-white border border-red-100 shadow-sm space-y-3">
        <h3 class="font-bold text-red-600 text-sm">Keluar dari Sesi</h3>
        <p class="text-xs text-slate-500">
            Keluar dari sesi saat ini di peramban ini. Anda dapat masuk kembali kapan saja dengan email dan password Anda.
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs md:text-sm font-semibold transition-all flex items-center gap-2">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Keluar dari Akun
            </button>
        </form>
    </div>
</div>
@endsection
