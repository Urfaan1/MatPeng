@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Detail Transaksi</h2>
            <p class="text-sm text-slate-500">Rincian aktivitas finansial yang tercatat</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    <div class="p-6 md:p-8 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-6">
        <!-- Header Nominal & Tipe -->
        <div class="flex items-center justify-between pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block mb-1">
                    {{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                </span>
                <h3 class="text-3xl font-black tracking-tight {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $transaction->type === 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </h3>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center {{ $transaction->type === 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                <i data-lucide="{{ $transaction->type === 'income' ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-7 h-7 stroke-[2.5]"></i>
            </div>
        </div>

        <!-- Detail Data Grid -->
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Kategori</dt>
                <dd class="font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $transaction->type === 'income' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    {{ $transaction->category->name ?? 'Kategori Dihapus' }}
                </dd>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Transaksi</dt>
                <dd class="font-bold text-slate-800">
                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </dd>
            </div>

            <div class="sm:col-span-2 p-4 rounded-xl bg-slate-50 border border-slate-100">
                <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Deskripsi / Catatan</dt>
                <dd class="text-slate-700 leading-relaxed">
                    {{ $transaction->description ?: 'Tidak ada deskripsi tambahan.' }}
                </dd>
            </div>

            <div class="sm:col-span-2 text-xs text-slate-400 pt-2 flex items-center justify-between">
                <span>Dicatat pada: {{ $transaction->created_at->locale('id')->isoFormat('D MMM Y, HH:mm') }} WIB</span>
                @if($transaction->updated_at != $transaction->created_at)
                    <span>Terakhir diubah: {{ $transaction->updated_at->locale('id')->isoFormat('D MMM Y, HH:mm') }} WIB</span>
                @endif
            </div>
        </dl>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <button type="button" 
                    onclick="if(confirm('Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.')) document.getElementById('delete-form').submit();"
                    class="px-4 py-2.5 rounded-xl text-red-600 hover:bg-red-50 text-sm font-semibold transition-all flex items-center gap-1.5">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Hapus Transaksi
            </button>

            <a href="{{ route('transactions.edit', $transaction->id) }}" 
               class="px-5 py-2.5 rounded-xl bg-[#123E34] hover:bg-[#1B594B] text-white text-sm font-semibold transition-all shadow-md flex items-center gap-1.5">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                Ubah Transaksi
            </a>
        </div>

        <form id="delete-form" method="POST" action="{{ route('transactions.destroy', $transaction->id) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
