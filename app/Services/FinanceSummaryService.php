<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinanceSummaryService
{
    /**
     * Hitung total saldo akumulatif seluruh waktu.
     */
    public function getTotalBalance(User $user): float
    {
        $totalIncome = (float) $user->transactions()->income()->sum('amount');
        $totalExpense = (float) $user->transactions()->expense()->sum('amount');

        return $totalIncome - $totalExpense;
    }

    /**
     * Ringkasan keuangan untuk periode bulan & tahun tertentu.
     */
    public function getMonthlySummary(User $user, ?int $year = null, ?int $month = null): array
    {
        $year = $year ?? (int) now()->format('Y');
        $month = $month ?? (int) now()->format('n');

        $monthlyTransactions = $user->transactions()
            ->forPeriod($year, $month)
            ->get();

        $monthlyIncome = (float) $monthlyTransactions->where('type', 'income')->sum('amount');
        $monthlyExpense = (float) $monthlyTransactions->where('type', 'expense')->sum('amount');
        $netCashFlow = $monthlyIncome - $monthlyExpense;
        $transactionCount = $monthlyTransactions->count();

        return [
            'year' => $year,
            'month' => $month,
            'period_label' => Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM Y'),
            'total_balance' => $this->getTotalBalance($user),
            'monthly_income' => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'net_cash_flow' => $netCashFlow,
            'transaction_count' => $transactionCount,
        ];
    }

    /**
     * Distribusi pengeluaran berdasarkan kategori untuk bulan tertentu.
     */
    public function getCategoryExpenseDistribution(User $user, ?int $year = null, ?int $month = null): Collection
    {
        $year = $year ?? (int) now()->format('Y');
        $month = $month ?? (int) now()->format('n');

        return $user->transactions()
            ->with('category')
            ->expense()
            ->forPeriod($year, $month)
            ->selectRaw('category_id, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('category_id')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($item) {
                return [
                    'category_name' => $item->category?->name ?? 'Lainnya',
                    'total_amount' => (float) $item->total_amount,
                    'count' => (int) $item->count,
                ];
            });
    }

    /**
     * Distribusi pemasukan berdasarkan kategori untuk bulan tertentu.
     */
    public function getCategoryIncomeDistribution(User $user, ?int $year = null, ?int $month = null): Collection
    {
        $year = $year ?? (int) now()->format('Y');
        $month = $month ?? (int) now()->format('n');

        return $user->transactions()
            ->with('category')
            ->income()
            ->forPeriod($year, $month)
            ->selectRaw('category_id, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('category_id')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($item) {
                return [
                    'category_name' => $item->category?->name ?? 'Lainnya',
                    'total_amount' => (float) $item->total_amount,
                    'count' => (int) $item->count,
                ];
            });
    }

    /**
     * Data tren 6 bulan terakhir untuk grafik.
     */
    public function getMonthlyTrend(User $user, int $months = 6): array
    {
        $labels = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $year = (int) $date->format('Y');
            $month = (int) $date->format('n');

            $labels[] = $date->locale('id')->isoFormat('MMM Y');

            $income = (float) $user->transactions()
                ->income()
                ->forPeriod($year, $month)
                ->sum('amount');

            $expense = (float) $user->transactions()
                ->expense()
                ->forPeriod($year, $month)
                ->sum('amount');

            $incomeData[] = $income;
            $expenseData[] = $expense;
        }

        return [
            'labels' => $labels,
            'income' => $incomeData,
            'expense' => $expenseData,
        ];
    }

    /**
     * Inisialisasi kategori default bagi user baru (Idempotent).
     */
    public function createDefaultCategories(User $user): void
    {
        $defaults = [
            'income' => ['Gaji', 'Bonus', 'Usaha', 'Hadiah', 'Lainnya'],
            'expense' => [
                'Makanan dan Minuman',
                'Transportasi',
                'Belanja',
                'Tagihan',
                'Pendidikan',
                'Kesehatan',
                'Hiburan',
                'Lainnya',
            ],
        ];

        foreach ($defaults as $type => $names) {
            foreach ($names as $name) {
                $user->categories()->firstOrCreate([
                    'name' => $name,
                    'type' => $type,
                ]);
            }
        }
    }
}
