@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Laporan Keuangan</h2>
            <p class="text-sm text-slate-500">Analisis kinerja arus kas dan pengeluaran Anda</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('invoices.show') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs md:text-sm font-bold transition-all shadow-sm">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                Lihat & Cetak Invoice
            </a>

            <!-- Filter Bulan & Tahun -->
            <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-2">
            <select name="month" class="px-3 py-2 text-xs md:text-sm rounded-xl border border-slate-200 bg-white">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                    </option>
                @endfor
            </select>

            <select name="year" class="px-3 py-2 text-xs md:text-sm rounded-xl border border-slate-200 bg-white">
                @for($y = (int) now()->format('Y') - 2; $y <= (int) now()->format('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-[#123E34] text-white text-xs md:text-sm font-semibold hover:bg-[#1B594B]">
                Tampilkan
            </button>
        </form>
    </div>

    <!-- Monthly Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Pemasukan</span>
            <span class="text-2xl font-bold text-emerald-600">+Rp {{ number_format($summary['monthly_income'], 0, ',', '.') }}</span>
        </div>

        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Pengeluaran</span>
            <span class="text-2xl font-bold text-red-600">-Rp {{ number_format($summary['monthly_expense'], 0, ',', '.') }}</span>
        </div>

        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Arus Kas Bersih (Net)</span>
            <span class="text-2xl font-bold {{ $summary['net_cash_flow'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                {{ $summary['net_cash_flow'] >= 0 ? '+' : '' }}Rp {{ number_format($summary['net_cash_flow'], 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Invoice & Download Quick Access Banner -->
    <div class="p-6 rounded-2xl bg-gradient-to-r from-[#123E34] to-[#1B594B] text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-md">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-400 text-slate-950">
                    Fitur Baru
                </span>
                <span class="text-xs text-emerald-200">Unduh & Rekapitulasi</span>
            </div>
            <h3 class="text-lg font-black tracking-tight">Lembar Invoice & Laporan Keuangan</h3>
            <p class="text-xs text-emerald-100/80 max-w-lg">
                Lihat ringkasan arus kas Anda selama 1 minggu, 1 bulan, atau 1 tahun penuh, lalu cetak sebagai PDF atau unduh file Excel/CSV.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('invoices.show', ['period' => 'week']) }}" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-sm transition-all">
                Seminggu
            </a>
            <a href="{{ route('invoices.show', ['period' => 'month']) }}" class="px-3 py-2 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 text-xs font-bold transition-all shadow-sm">
                Sebulan
            </a>
            <a href="{{ route('invoices.show', ['period' => 'year']) }}" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-sm transition-all">
                Setahun
            </a>
        </div>
    </div>
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm text-center py-12">
        <div class="w-12 h-12 rounded-full bg-emerald-50 text-[#123E34] flex items-center justify-center mx-auto mb-3">
            <i data-lucide="bar-chart-2" class="w-6 h-6 stroke-[2]"></i>
        </div>
        <h3 class="text-base font-bold text-slate-800">Laporan Lengkap & Analisis Grafik</h3>
        <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
            Fitur analisis mendalam serta grafik interaktif per kategori akan dihubungkan secara menyeluruh dengan data riil pada Tahap 5.
        </p>
    </div>
</div>
@endsection
