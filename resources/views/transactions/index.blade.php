@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Riwayat Transaksi</h2>
            <p class="text-sm text-slate-500">Kelola dan pantau semua transaksi keuangan Anda</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#123E34] hover:bg-[#1B594B] text-white text-sm font-semibold transition-all shadow-md active:scale-95">
            <i data-lucide="plus" class="w-4 h-4 stroke-[2.5]"></i>
            Tambah Transaksi
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm" x-data="{ expanded: {{ request()->hasAny(['start_date', 'end_date']) ? 'true' : 'false' }} }">
        <form method="GET" action="{{ route('transactions.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Cari Deskripsi</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Gaji, Kopi..." 
                               class="w-full pl-9 pr-3 py-2 text-xs md:text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Jenis</label>
                    <select name="type" class="w-full px-3 py-2 text-xs md:text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34]">
                        <option value="">Semua Jenis</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Kategori</label>
                    <select name="category_id" class="w-full px-3 py-2 text-xs md:text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34]">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->type == 'income' ? 'Masuk' : 'Keluar' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-[#123E34] text-white text-xs md:text-sm font-semibold hover:bg-[#1B594B] transition-all">
                        Terapkan
                    </button>
                    <button type="button" @click="expanded = !expanded" class="py-2 px-3 rounded-xl border border-slate-200 text-slate-600 text-xs md:text-sm hover:bg-slate-50" title="Filter Tanggal">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </button>
                    @if(request()->hasAny(['type', 'category_id', 'search', 'start_date', 'end_date']))
                        <a href="{{ route('transactions.index') }}" class="py-2 px-3 rounded-xl border border-slate-200 text-red-600 text-xs md:text-sm hover:bg-red-50" title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Rentang Tanggal Ekstra -->
            <div x-show="expanded" style="display: none;" class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full px-3 py-2 text-xs md:text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full px-3 py-2 text-xs md:text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34]">
                </div>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-3">Tanggal</th>
                        <th class="py-3 px-3">Deskripsi</th>
                        <th class="py-3 px-3">Kategori</th>
                        <th class="py-3 px-3 text-right">Nominal</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="py-3.5 px-3 text-xs text-slate-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($tx->transaction_date)->locale('id')->isoFormat('D MMM Y') }}
                            </td>
                            <td class="py-3.5 px-3 font-medium text-slate-800">
                                <a href="{{ route('transactions.show', $tx->id) }}" class="flex items-center gap-2.5 hover:text-[#123E34]">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $tx->type === 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                                        <i data-lucide="{{ $tx->type === 'income' ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-4 h-4 stroke-[2.5]"></i>
                                    </div>
                                    <span class="truncate max-w-xs">{{ $tx->description ?: ($tx->type === 'income' ? 'Pemasukan' : 'Pengeluaran') }}</span>
                                </a>
                            </td>
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $tx->category->name ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-right font-bold whitespace-nowrap {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('transactions.show', $tx->id) }}" title="Lihat Detail" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('transactions.edit', $tx->id) }}" title="Edit Transaksi" class="p-1.5 rounded-lg text-slate-400 hover:text-[#123E34] hover:bg-emerald-50 transition-colors">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <button type="button" 
                                            onclick="if(confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) document.getElementById('del-tx-{{ $tx->id }}').submit();"
                                            title="Hapus Transaksi" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                    <form id="del-tx-{{ $tx->id }}" method="POST" action="{{ route('transactions.destroy', $tx->id) }}" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="text-sm font-medium">Belum ada transaksi ditemukan</p>
                                <p class="text-xs text-slate-400 mt-1">
                                    @if(request()->hasAny(['type', 'category_id', 'search', 'start_date', 'end_date']))
                                        Tidak ada transaksi yang cocok dengan filter yang dipilih.
                                    @else
                                        Mulai catat pemasukan atau pengeluaran pertama Anda.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="mt-4 pt-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
