@extends('layouts.app')

@section('title', 'Invoice Keuangan — ' . $periodLabel)

@section('content')
<div class="space-y-6">
    <!-- Action Toolbar (Hidden during Print) -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                    Invoice & Rekap Keuangan
                </span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">Laporan & Lembar Invoice</h2>
            <p class="text-xs text-slate-500">Pilih periode seminggu, sebulan, atau setahun dan unduh laporannya.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Period Selector Tabs -->
            <div class="flex items-center p-1 bg-slate-100 rounded-xl">
                <a href="{{ route('invoices.show', ['period' => 'week']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $period === 'week' ? 'bg-white text-[#123E34] shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    1 Minggu
                </a>
                <a href="{{ route('invoices.show', ['period' => 'month']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $period === 'month' ? 'bg-white text-[#123E34] shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    1 Bulan
                </a>
                <a href="{{ route('invoices.show', ['period' => 'year']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $period === 'year' ? 'bg-white text-[#123E34] shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    1 Tahun
                </a>
            </div>

            <!-- Download CSV -->
            <a href="{{ route('invoices.export-csv', ['period' => $period]) }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-700"></i>
                Unduh Excel/CSV
            </a>

            <!-- Print / Download PDF Button -->
            <button type="button" 
                    onclick="window.print()" 
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#123E34] hover:bg-[#1B594B] text-white text-xs font-bold transition-all shadow-md active:scale-95">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Printable Invoice Sheet Area -->
    <div id="invoice-sheet" class="bg-white rounded-2xl border border-slate-200/90 shadow-lg p-6 sm:p-10 max-w-4xl mx-auto space-y-8">
        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200 gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-[#123E34] text-emerald-400 flex items-center justify-center shadow-md">
                    <i data-lucide="wallet" class="w-7 h-7 stroke-[2.5]"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#123E34] tracking-tight">MatPeng</h1>
                    <p class="text-xs text-slate-500 font-medium">Layanan Manajemen Keuangan Pribadi</p>
                </div>
            </div>

            <div class="sm:text-right">
                <span class="inline-block px-3 py-1 rounded-md text-xs font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                    Lembar Rekap Transaksi
                </span>
                <p class="text-xs font-mono text-slate-500 mt-1.5 font-bold">{{ $invoiceNumber }}</p>
                <p class="text-[11px] text-slate-400">Dicetak: {{ now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB</p>
            </div>
        </div>

        <!-- Details Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-5 rounded-xl bg-slate-50/80 border border-slate-100 text-xs">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Diterbitkan Untuk Pengguna</p>
                <p class="text-base font-extrabold text-slate-900">{{ $user->name }}</p>
                <p class="text-slate-600 mt-0.5">{{ $user->email }}</p>
                <p class="text-[11px] text-emerald-700 font-medium mt-1">ID Pengguna: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>

            <div class="sm:text-right space-y-1">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cakupan Periode Laporan</p>
                <p class="text-sm font-bold text-slate-800">{{ $periodLabel }}</p>
                <p class="text-slate-600 font-medium">
                    {{ $startDate->locale('id')->isoFormat('D MMM Y') }} &mdash; {{ $endDate->locale('id')->isoFormat('D MMM Y') }}
                </p>
                <p class="text-[11px] text-slate-500">Total Transaksi: <b>{{ $transactionCount }} catatan</b></p>
            </div>
        </div>

        <!-- Financial Summary Banner -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/50">
                <p class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Total Pemasukan</p>
                <p class="text-xl font-black text-emerald-700 mt-1">
                    +Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </p>
            </div>

            <div class="p-4 rounded-xl border border-rose-200 bg-rose-50/50">
                <p class="text-[11px] font-bold text-rose-800 uppercase tracking-wider">Total Pengeluaran</p>
                <p class="text-xl font-black text-rose-600 mt-1">
                    -Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </p>
            </div>

            <div class="p-4 rounded-xl border {{ $netCashFlow >= 0 ? 'border-teal-200 bg-teal-50/50' : 'border-amber-200 bg-amber-50/50' }}">
                <p class="text-[11px] font-bold {{ $netCashFlow >= 0 ? 'text-teal-900' : 'text-amber-900' }} uppercase tracking-wider">
                    Arus Kas Bersih (Surplus/Defisit)
                </p>
                <p class="text-xl font-black {{ $netCashFlow >= 0 ? 'text-teal-800' : 'text-amber-800' }} mt-1">
                    {{ $netCashFlow >= 0 ? '+' : '' }}Rp {{ number_format($netCashFlow, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Transactions Itemized Table -->
        <div class="overflow-hidden border border-slate-200 rounded-xl">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-[10px] uppercase font-bold text-slate-600 tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 w-12 text-center">No</th>
                        <th class="py-3 px-3 w-28">Tanggal</th>
                        <th class="py-3 px-4">Kategori & Keterangan</th>
                        <th class="py-3 px-3 w-24 text-center">Tipe</th>
                        <th class="py-3 px-4 w-36 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $index => $t)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-2.5 px-3 font-medium text-slate-600">
                                {{ \Carbon\Carbon::parse($t->transaction_date)->locale('id')->isoFormat('D MMM Y') }}
                            </td>
                            <td class="py-2.5 px-4">
                                <p class="font-bold text-slate-900">{{ $t->category ? $t->category->name : 'Tanpa Kategori' }}</p>
                                @if($t->description)
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $t->description }}</p>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if($t->type === 'income')
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Pemasukan
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Pengeluaran
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold {{ $t->type === 'income' ? 'text-emerald-600' : 'text-slate-800' }}">
                                {{ $t->type === 'income' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                Belum ada transaksi yang tercatat pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-xs">
                    <tr>
                        <td colspan="4" class="py-3 px-4 text-right uppercase tracking-wider text-slate-600">
                            Total Arus Kas Bersih
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-sm {{ $netCashFlow >= 0 ? 'text-teal-800' : 'text-rose-700' }}">
                            {{ $netCashFlow >= 0 ? '+' : '' }}Rp {{ number_format($netCashFlow, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer Notes & Signature -->
        <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between text-[11px] text-slate-400 gap-4">
            <div>
                <p class="font-bold text-slate-700">Catatan Keuangan:</p>
                <p>Dokumen ini diterbitkan secara otomatis oleh sistem MatPeng dan sah sebagai bukti rekapitulasi keuangan pribadi.</p>
            </div>
            <div class="sm:text-right shrink-0">
                <div class="inline-block border-b-2 border-slate-300 pb-1 px-4 mb-1">
                    <p class="font-mono text-[10px] font-bold text-emerald-800">MATPENG SYSTEM VERIFIED</p>
                </div>
                <p class="text-[10px] text-slate-400">Otomatisasi Laporan & Arus Kas</p>
            </div>
        </div>
    </div>
</div>

<!-- Print Stylesheet -->
<style>
@media print {
    /* Sembunyikan elemen navigasi dan tombol non-cetak */
    aside, header, nav, .no-print, footer {
        display: none !important;
    }

    body {
        background: white !important;
        color: black !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    #invoice-sheet {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    main {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
    }
}
</style>
@endsection
