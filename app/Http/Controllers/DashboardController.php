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

        // Jika user telah login, ambil data riil dari database
        if ($user) {
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
        } else {
            // Data ilustrasi awal untuk preview tahap 1 (sebelum tahap autentikasi diaktifkan penuh)
            $isIllustration = true;
            $summary = [
                'period_label' => Carbon::now()->locale('id')->isoFormat('MMMM Y'),
                'total_balance' => 8450000,
                'monthly_income' => 12500000,
                'monthly_expense' => 4050000,
                'net_cash_flow' => 8450000,
                'transaction_count' => 18,
            ];

            $recentTransactions = collect([
                (object)[
                    'id' => 1,
                    'type' => 'income',
                    'amount' => 10000000,
                    'transaction_date' => Carbon::now()->subDays(1),
                    'description' => 'Gaji Bulanan PT Maju Bersama',
                    'category' => (object)['name' => 'Gaji', 'type' => 'income'],
                ],
                (object)[
                    'id' => 2,
                    'type' => 'expense',
                    'amount' => 1850000,
                    'transaction_date' => Carbon::now()->subDays(2),
                    'description' => 'Belanja Bulanan Supermarket',
                    'category' => (object)['name' => 'Makanan dan Minuman', 'type' => 'expense'],
                ],
                (object)[
                    'id' => 3,
                    'type' => 'expense',
                    'amount' => 450000,
                    'transaction_date' => Carbon::now()->subDays(3),
                    'description' => 'Tagihan Listrik & Internet',
                    'category' => (object)['name' => 'Tagihan', 'type' => 'expense'],
                ],
                (object)[
                    'id' => 4,
                    'type' => 'income',
                    'amount' => 2500000,
                    'transaction_date' => Carbon::now()->subDays(4),
                    'description' => 'Bonus Proyek Freelance Web',
                    'category' => (object)['name' => 'Bonus', 'type' => 'income'],
                ],
                (object)[
                    'id' => 5,
                    'type' => 'expense',
                    'amount' => 250000,
                    'transaction_date' => Carbon::now()->subDays(5),
                    'description' => 'Bahan Bakar Bensin Motor',
                    'category' => (object)['name' => 'Transportasi', 'type' => 'expense'],
                ],
            ]);

            $categoryExpenses = collect([
                ['category_name' => 'Makanan dan Minuman', 'total_amount' => 1850000, 'count' => 8],
                ['category_name' => 'Tagihan', 'total_amount' => 950000, 'count' => 3],
                ['category_name' => 'Transportasi', 'total_amount' => 650000, 'count' => 5],
                ['category_name' => 'Hiburan', 'total_amount' => 600000, 'count' => 2],
            ]);

            $trend = [
                'labels' => ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
                'income' => [9500000, 10000000, 11000000, 10500000, 11500000, 12500000],
                'expense' => [3800000, 4200000, 4100000, 3900000, 4300000, 4050000],
            ];
        }

        return view('dashboard.index', compact(
            'summary',
            'recentTransactions',
            'categoryExpenses',
            'trend',
            'isIllustration'
        ));
    }
}
