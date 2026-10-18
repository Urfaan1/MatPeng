@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Kategori</h2>
            <p class="text-sm text-slate-500">Kelola kelompok kategori untuk pemasukan dan pengeluaran</p>
        </div>
        <button type="button" 
                onclick="alert('Fitur tambah kategori kustom akan diaktifkan penuh pada Tahap 5')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#123E34] hover:bg-[#1B594B] text-white text-sm font-semibold transition-all shadow-md active:scale-95">
            <i data-lucide="plus" class="w-4 h-4 stroke-[2.5]"></i>
            Tambah Kategori
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Pemasukan -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h3 class="font-bold text-slate-800">Kategori Pemasukan</h3>
                </div>
                <span class="text-xs text-slate-400">Default & Kustom</span>
            </div>

            <div class="space-y-2">
                @php
                    $incomeCats = $categories->where('type', 'income');
                @endphp
                @forelse($incomeCats as $cat)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-emerald-50/50 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="arrow-down-left" class="w-4 h-4 text-emerald-600"></i>
                            <span class="text-sm font-medium text-slate-800">{{ $cat->name }}</span>
                        </div>
                        <span class="text-xs text-slate-400">{{ $cat->transactions_count ?? 0 }} transaksi</span>
                    </div>
                @empty
                    @foreach(['Gaji', 'Bonus', 'Usaha', 'Hadiah', 'Lainnya'] as $defName)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-emerald-50/50 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="arrow-down-left" class="w-4 h-4 text-emerald-600"></i>
                                <span class="text-sm font-medium text-slate-800">{{ $defName }}</span>
                            </div>
                            <span class="text-xs text-slate-400">Kategori Bawaan</span>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>

        <!-- Pengeluaran -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <h3 class="font-bold text-slate-800">Kategori Pengeluaran</h3>
                </div>
                <span class="text-xs text-slate-400">Default & Kustom</span>
            </div>

            <div class="space-y-2">
                @php
                    $expenseCats = $categories->where('type', 'expense');
                @endphp
                @forelse($expenseCats as $cat)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-red-50/50 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="arrow-up-right" class="w-4 h-4 text-red-600"></i>
                            <span class="text-sm font-medium text-slate-800">{{ $cat->name }}</span>
                        </div>
                        <span class="text-xs text-slate-400">{{ $cat->transactions_count ?? 0 }} transaksi</span>
                    </div>
                @empty
                    @foreach(['Makanan dan Minuman', 'Transportasi', 'Belanja', 'Tagihan', 'Pendidikan', 'Kesehatan', 'Hiburan', 'Lainnya'] as $defName)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-red-50/50 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="arrow-up-right" class="w-4 h-4 text-red-600"></i>
                                <span class="text-sm font-medium text-slate-800">{{ $defName }}</span>
                            </div>
                            <span class="text-xs text-slate-400">Kategori Bawaan</span>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
