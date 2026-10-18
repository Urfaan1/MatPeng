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
    <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
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
                    Filter
                </button>
                <a href="{{ route('transactions.index') }}" class="py-2 px-3 rounded-xl border border-slate-200 text-slate-600 text-xs md:text-sm hover:bg-slate-50">
                    Reset
                </a>
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
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-3 text-xs text-slate-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($tx->transaction_date)->locale('id')->isoFormat('D MMM Y') }}
                            </td>
                            <td class="py-3 px-3 font-medium text-slate-800">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $tx->type === 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                                        <i data-lucide="{{ $tx->type === 'income' ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-4 h-4 stroke-[2.5]"></i>
                                    </div>
                                    <span>{{ $tx->description ?: ($tx->type === 'income' ? 'Pemasukan' : 'Pengeluaran') }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $tx->category->name ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right font-bold whitespace-nowrap {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right whitespace-nowrap">
                                <a href="{{ route('transactions.edit', $tx->id) }}" class="text-slate-400 hover:text-slate-700 p-1">
                                    <i data-lucide="edit-3" class="w-4 h-4 inline"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="text-sm font-medium">Belum ada transaksi ditemukan</p>
                                <p class="text-xs text-slate-400 mt-1">Mulai catat pemasukan atau pengeluaran pertama Anda</p>
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
