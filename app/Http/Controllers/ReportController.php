<?php

namespace App\Http\Controllers;

use App\Services\FinanceSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(
        protected FinanceSummaryService $financeService
    ) {}

    public function index(Request $request)
    {
        $year = (int) ($request->year ?? now()->format('Y'));
        $month = (int) ($request->month ?? now()->format('n'));
        $user = Auth::user();

        if ($user) {
            $summary = $this->financeService->getMonthlySummary($user, $year, $month);
            $categoryExpenses = $this->financeService->getCategoryExpenseDistribution($user, $year, $month);
            $trend = $this->financeService->getMonthlyTrend($user, 12);
        } else {
            $summary = [
                'period_label' => now()->locale('id')->isoFormat('MMMM Y'),
                'total_balance' => 8450000,
                'monthly_income' => 12500000,
                'monthly_expense' => 4050000,
                'net_cash_flow' => 8450000,
                'transaction_count' => 18,
            ];
            $categoryExpenses = collect();
            $trend = ['labels' => [], 'income' => [], 'expense' => []];
        }

        return view('reports.index', compact('summary', 'categoryExpenses', 'trend', 'year', 'month'));
    }
}
