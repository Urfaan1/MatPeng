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

        $summary = $this->financeService->getMonthlySummary($user, $year, $month);
        $categoryExpenses = $this->financeService->getCategoryExpenseDistribution($user, $year, $month);
        $trend = $this->financeService->getMonthlyTrend($user, 12);

        return view('reports.index', compact('summary', 'categoryExpenses', 'trend', 'year', 'month'));
    }
}
