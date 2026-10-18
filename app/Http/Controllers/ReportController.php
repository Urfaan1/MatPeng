<?php

namespace App\Http\Controllers;

use App\Services\FinanceSummaryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(
        protected FinanceSummaryService $financeService
    ) {}

    /**
     * Tampilkan laporan keuangan bulanan lengkap dengan filter dan visualisasi.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $year = (int) ($request->input('year') ?: now()->format('Y'));
        $month = (int) ($request->input('month') ?: now()->format('n'));

        // Pastikan input bulan dan tahun valid
        if ($month < 1 || $month > 12) {
            $month = (int) now()->format('n');
        }

        $summary = $this->financeService->getMonthlySummary($user, $year, $month);
        $categoryExpenses = $this->financeService->getCategoryExpenseDistribution($user, $year, $month);
        $categoryIncomes = $this->financeService->getCategoryIncomeDistribution($user, $year, $month);
        $trend = $this->financeService->getMonthlyTrend($user, 12);

        $periodTransactions = $user->transactions()
            ->with('category')
            ->forPeriod($year, $month)
            ->latest('transaction_date')
            ->latest('id')
            ->get();

        $availableYears = range((int) now()->format('Y') - 3, (int) now()->format('Y') + 1);

        return view('reports.index', compact(
            'summary',
            'categoryExpenses',
            'categoryIncomes',
            'trend',
            'periodTransactions',
            'year',
            'month',
            'availableYears'
        ));
    }
}
