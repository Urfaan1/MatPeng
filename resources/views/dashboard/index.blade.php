@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Top Header & Sapaan -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Halo, {{ Auth::user()->name }}! 👋
                </h2>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Ringkasan kesehatan finansial Anda untuk periode <span class="font-semibold text-slate-700">{{ $summary['period_label'] }}</span>.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#123E34] hover:bg-[#1B594B] text-white text-sm font-semibold transition-all shadow-md active:scale-95">
                <i data-lucide="plus" class="w-4 h-4 stroke-[2.5]"></i>
                Catat Transaksi
            </a>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Hero Card: Total Saldo Riil -->
        <div class="md:col-span-2 lg:col-span-2 p-6 rounded-2xl {{ $summary['total_balance'] < 0 ? 'bg-gradient-to-br from-[#7F1D1D] to-[#991B1B] shadow-red-950/20' : 'bg-gradient-to-br from-[#123E34] to-[#1e5c4e] shadow-emerald-950/15' }} text-white shadow-xl relative overflow-hidden flex flex-col justify-between">
            <div class="absolute -right-8 -bottom-8 w-44 h-44 rounded-full {{ $summary['total_balance'] < 0 ? 'bg-red-400/10' : 'bg-emerald-400/10' }} blur-2xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider {{ $summary['total_balance'] < 0 ? 'text-red-200/90' : 'text-emerald-200/90' }}">
                    Total Saldo Terkini
                </span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $summary['total_balance'] < 0 ? 'bg-red-900/80 text-red-100 border border-red-700/60' : 'bg-emerald-900/60 text-emerald-200 border border-emerald-700/50' }} flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ $summary['total_balance'] < 0 ? 'bg-red-300' : 'bg-emerald-400' }} animate-pulse"></span>
                    {{ $summary['total_balance'] < 0 ? 'Defisit' : 'Akumulatif' }}
                </span>
            </div>

            <div class="my-2">
                <h3 class="text-3xl md:text-4xl font-extrabold tracking-tight">
                    {{ $summary['total_balance'] < 0 ? '-Rp ' . number_format(abs($summary['total_balance']), 0, ',', '.') : 'Rp ' . number_format($summary['total_balance'], 0, ',', '.') }}
                </h3>
                <p class="text-xs {{ $summary['total_balance'] < 0 ? 'text-red-200/80' : 'text-emerald-200/70' }} mt-1">
                    {{ $summary['total_balance'] < 0 ? 'Perhatian: Total pengeluaran Anda saat ini melampaui total pemasukan.' : 'Saldo riil dihitung dari selisih total pemasukan dan pengeluaran sepanjang waktu.' }}
                </p>
            </div>

            <div class="pt-4 mt-2 border-t {{ $summary['total_balance'] < 0 ? 'border-red-700/50' : 'border-emerald-700/50' }} grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="{{ $summary['total_balance'] < 0 ? 'text-red-200/80' : 'text-emerald-300/80' }} block">Arus Kas Bulan Ini</span>
                    <span class="font-bold text-sm text-white">
                        {{ $summary['net_cash_flow'] >= 0 ? '+' : '-' }}Rp {{ number_format(abs($summary['net_cash_flow']), 0, ',', '.') }}
                    </span>
                </div>
                <div>
                    <span class="{{ $summary['total_balance'] < 0 ? 'text-red-200/80' : 'text-emerald-300/80' }} block">Jumlah Transaksi Bulan Ini</span>
                    <span class="font-bold text-sm text-white">{{ $summary['transaction_count'] }} Transaksi</span>
                </div>
            </div>
        </div>

        <!-- Card: Pemasukan Bulan Berjalan -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-emerald-200 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemasukan Bulan Ini</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="arrow-down-left" class="w-5 h-5 stroke-[2.5]"></i>
                </div>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-emerald-600 tracking-tight">
                    +Rp {{ number_format($summary['monthly_income'], 0, ',', '.') }}
                </h4>
                <p class="text-xs text-slate-400 mt-1">Uang masuk bulan berjalan</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs text-emerald-700 font-medium">
                <i data-lucide="trending-up" class="w-3.5 h-3.5 mr-1"></i>
                Pencatatan aktif
            </div>
        </div>

        <!-- Card: Pengeluaran Bulan Berjalan -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-red-200 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengeluaran Bulan Ini</span>
                <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                    <i data-lucide="arrow-up-right" class="w-5 h-5 stroke-[2.5]"></i>
                </div>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-red-600 tracking-tight">
                    -Rp {{ number_format($summary['monthly_expense'], 0, ',', '.') }}
                </h4>
                <p class="text-xs text-slate-400 mt-1">Uang keluar bulan berjalan</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs text-slate-500 font-medium">
                <i data-lucide="activity" class="w-3.5 h-3.5 mr-1 text-slate-400"></i>
                Terkontrol
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Trend Line/Bar Chart -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Tren Pemasukan & Pengeluaran</h3>
                    <p class="text-xs text-slate-400">Perbandingan arus kas 6 bulan terakhir</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-medium">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-emerald-600"></span>
                        <span class="text-slate-600">Masuk</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-red-500"></span>
                        <span class="text-slate-600">Keluar</span>
                    </div>
                </div>
            </div>

            @php
                $hasTrendData = array_sum($trend['income']) > 0 || array_sum($trend['expense']) > 0;
            @endphp

            @if($hasTrendData)
                <div class="relative h-64 w-full">
                    <canvas id="trendChart"></canvas>
                </div>
            @else
                <div class="h-64 flex flex-col items-center justify-center text-center p-6 bg-slate-50/60 rounded-xl border border-dashed border-slate-200">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                        <i data-lucide="bar-chart-2" class="w-6 h-6 stroke-[1.5]"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Belum ada aktivitas transaksi</p>
                    <p class="text-xs text-slate-400 max-w-sm mt-1">
                        Grafik tren akan otomatis muncul seiring Anda mulai mencatat pemasukan dan pengeluaran harian.
                    </p>
                    <a href="{{ route('transactions.create') }}" class="mt-3 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[#123E34] text-white text-xs font-semibold hover:bg-[#1B594B]">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        Catat Sekarang
                    </a>
                </div>
            @endif
        </div>

        <!-- Category Expense Donut Chart -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="mb-4">
                <h3 class="text-base font-bold text-slate-900">Distribusi Kategori</h3>
                <p class="text-xs text-slate-400">Proporsi pengeluaran bulan ini</p>
            </div>
            
            @if($categoryExpenses->isNotEmpty())
                <div class="relative h-48 w-full flex items-center justify-center">
                    <canvas id="categoryChart"></canvas>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 space-y-2 max-h-36 overflow-y-auto">
                    @foreach($categoryExpenses as $cat)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-600 truncate max-w-[140px]">{{ $cat['category_name'] }}</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($cat['total_amount'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-48 flex flex-col items-center justify-center text-center p-6 bg-slate-50/60 rounded-xl border border-dashed border-slate-200">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                        <i data-lucide="pie-chart" class="w-5 h-5 stroke-[1.5]"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-600">Belum ada pengeluaran</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pengeluaran bulan ini masih Rp 0</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Transactions Section -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Transaksi Terbaru</h3>
                <p class="text-xs text-slate-400">5 aktivitas finansial mutakhir di akun Anda</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#123E34] hover:text-[#1B594B]">
                Lihat Semua
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>

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
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50/80 transition-colors">
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700">
                                    {{ $tx->category->name ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-right font-bold whitespace-nowrap {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3 text-right whitespace-nowrap">
                                <a href="{{ route('transactions.show', $tx->id) }}" title="Lihat Detail" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors inline-block">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-300 mx-auto mb-2">
                                    <i data-lucide="inbox" class="w-6 h-6 stroke-[1.5]"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-700">Belum ada transaksi yang dicatat</p>
                                <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">
                                    Catat pengeluaran atau pemasukan pertama Anda untuk melihat ringkasan keuangan di sini.
                                </p>
                                <a href="{{ route('transactions.create') }}" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#123E34] text-white text-xs font-semibold hover:bg-[#1B594B] shadow-sm">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    Catat Transaksi Pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Trend Chart (Bar)
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: @json($trend['labels']),
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: @json($trend['income']),
                        backgroundColor: '#16A34A',
                        borderRadius: 6,
                        maxBarThickness: 24,
                    },
                    {
                        label: 'Pengeluaran',
                        data: @json($trend['expense']),
                        backgroundColor: '#DC2626',
                        borderRadius: 6,
                        maxBarThickness: 24,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + window.formatRupiah(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000) + ' Jt';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000) + ' Rb';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    // Category Donut Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        const catLabels = @json($categoryExpenses->pluck('category_name'));
        const catData = @json($categoryExpenses->pluck('total_amount'));
        
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catData,
                    backgroundColor: [
                        '#123E34',
                        '#16A34A',
                        '#F59E0B',
                        '#EF4444',
                        '#6366F1',
                        '#EC4899',
                        '#8B5CF6',
                        '#06B6D4',
                        '#14B8A6',
                        '#F97316'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + window.formatRupiah(context.raw);
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
