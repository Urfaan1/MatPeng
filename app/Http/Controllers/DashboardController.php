<?php

namespace App\Http\Controllers;

use App\Services\FinanceSummaryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected FinanceSummaryService $financeService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();

        $summary = $this->financeService->getMonthlySummary($user);
        $recentTransactions = $user->transactions()
            ->with('category')
            ->latest('transaction_date')
            ->latest('id')
            ->take(5)
            ->get();
        $categoryExpenses = $this->financeService->getCategoryExpenseDistribution($user);
        $trend = $this->financeService->getMonthlyTrend($user);
        $isIllustration = false;

        return view('dashboard.index', compact(
            'summary',
            'recentTransactions',
            'categoryExpenses',
            'trend',
            'isIllustration'
        ));
    }
}
