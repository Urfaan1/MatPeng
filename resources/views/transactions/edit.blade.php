@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Transaksi</h2>
            <p class="text-sm text-slate-500">Perbarui rincian transaksi</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-all">
            <i data-lucide="x" class="w-5 h-5"></i>
        </a>
    </div>

    <div class="p-6 md:p-8 rounded-2xl bg-white border border-slate-200/80 shadow-sm"
         x-data="{ 
            type: '{{ old('type', $transaction->type) }}',
            amount: '{{ old('amount', $transaction->amount) }}',
            categories: @json($categories),
            get filteredCategories() {
                return this.categories.filter(c => c.type === this.type);
            }
         }">
        
        <form method="POST" action="{{ route('transactions.update', $transaction->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Toggle Pemasukan / Pengeluaran -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jenis Transaksi</label>
                <div class="grid grid-cols-2 gap-3 p-1 rounded-xl bg-slate-100">
                    <button type="button" 
                            @click="type = 'expense'"
                            :class="type === 'expense' ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="py-2.5 px-4 rounded-lg font-semibold text-sm transition-all flex items-center justify-center gap-2">
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                        Pengeluaran
                    </button>
                    <button type="button" 
                            @click="type = 'income'"
                            :class="type === 'income' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="py-2.5 px-4 rounded-lg font-semibold text-sm transition-all flex items-center justify-center gap-2">
                        <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                        Pemasukan
                    </button>
                </div>
                <input type="hidden" name="type" :value="type">
            </div>

            <!-- Nominal Transaksi -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nominal (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-sm font-bold text-slate-400">Rp</span>
                    <input type="number" 
                           name="amount" 
                           x-model="amount"
                           step="0.01"
                           min="1"
                           required
                           class="w-full pl-12 pr-4 py-3 text-lg font-bold rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34]">
                </div>
                <template x-if="amount > 0">
                    <p class="text-xs font-medium text-slate-500 mt-1" x-text="'Nominal: ' + window.formatRupiah(amount)"></p>
                </template>
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Kategori</label>
                <select name="category_id" required class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34]">
                    <template x-for="cat in filteredCategories" :key="cat.id">
                        <option :value="cat.id" :selected="cat.id == {{ $transaction->category_id }}" x-text="cat.name"></option>
                    </template>
                </select>
            </div>

            <!-- Tanggal Transaksi -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Transaksi</label>
                <input type="date" 
                       name="transaction_date" 
                       value="{{ old('transaction_date', $transaction->transaction_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" 
                       required
                       class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34]">
            </div>

            <!-- Deskripsi (Opsional) -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Deskripsi / Catatan</label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34]">{{ old('description', $transaction->description) }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" 
                        onclick="if(confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) document.getElementById('delete-form').submit();"
                        class="px-4 py-2.5 rounded-xl text-red-600 hover:bg-red-50 text-sm font-semibold transition-all flex items-center gap-1.5">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Hapus
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('transactions.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#123E34] hover:bg-[#1B594B] text-white text-sm font-semibold transition-all shadow-md active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-form" method="POST" action="{{ route('transactions.destroy', $transaction->id) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
