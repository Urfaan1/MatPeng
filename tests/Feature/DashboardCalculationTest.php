<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FinanceSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $incomeCategory;
    protected Category $expenseCategory;
    protected FinanceSummaryService $financeService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->financeService = new FinanceSummaryService();
        $this->financeService->createDefaultCategories($this->user);

        $this->incomeCategory = $this->user->categories()->where('type', 'income')->first();
        $this->expenseCategory = $this->user->categories()->where('type', 'expense')->first();
    }

    public function test_standard_financial_calculation_yields_exact_balance(): void
    {
        // Sesuai standar uji Bagian 13: Pemasukan 5.000.000 dan pengeluaran 1.750.000 -> saldo 3.250.000
        $this->user->transactions()->create([
            'category_id' => $this->incomeCategory->id,
            'type' => 'income',
            'amount' => 5000000.00,
            'transaction_date' => now()->format('Y-m-d'),
            'description' => 'Gaji Pokok',
        ]);

        $this->user->transactions()->create([
            'category_id' => $this->expenseCategory->id,
            'type' => 'expense',
            'amount' => 1750000.00,
            'transaction_date' => now()->format('Y-m-d'),
            'description' => 'Biaya Hidup',
        ]);

        $totalBalance = $this->financeService->getTotalBalance($this->user);
        $this->assertEquals(3250000.00, $totalBalance);

        // Uji tampilan di response dashboard
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('3.250.000');
        $response->assertSee('5.000.000');
        $response->assertSee('1.750.000');
    }

    public function test_updating_transaction_immediately_updates_dashboard_balance(): void
    {
        $tx = $this->user->transactions()->create([
            'category_id' => $this->incomeCategory->id,
            'type' => 'income',
            'amount' => 2000000.00,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $this->assertEquals(2000000.00, $this->financeService->getTotalBalance($this->user));

        // Ubah nominal transaksi menjadi 3.500.000
        $tx->update(['amount' => 3500000.00]);

        $this->assertEquals(3500000.00, $this->financeService->getTotalBalance($this->user));

        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertSee('3.500.000');
    }

    public function test_deleting_transaction_immediately_updates_dashboard_balance(): void
    {
        $tx1 = $this->user->transactions()->create([
            'category_id' => $this->incomeCategory->id,
            'type' => 'income',
            'amount' => 4000000.00,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $tx2 = $this->user->transactions()->create([
            'category_id' => $this->expenseCategory->id,
            'type' => 'expense',
            'amount' => 1000000.00,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $this->assertEquals(3000000.00, $this->financeService->getTotalBalance($this->user));

        // Hapus pengeluaran tx2
        $tx2->delete();

        $this->assertEquals(4000000.00, $this->financeService->getTotalBalance($this->user));
    }

    public function test_monthly_filter_aggregates_correct_period_transactions(): void
    {
        // Transaksi bulan berjalan
        $this->user->transactions()->create([
            'category_id' => $this->incomeCategory->id,
            'type' => 'income',
            'amount' => 6000000.00,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        // Transaksi bulan lalu
        $this->user->transactions()->create([
            'category_id' => $this->incomeCategory->id,
            'type' => 'income',
            'amount' => 4500000.00,
            'transaction_date' => now()->subMonth()->format('Y-m-d'),
        ]);

        $currentSummary = $this->financeService->getMonthlySummary($this->user);
        $this->assertEquals(6000000.00, $currentSummary['monthly_income']);

        // Saldo total mencakup seluruh waktu (6.000.000 + 4.500.000 = 10.500.000)
        $this->assertEquals(10500000.00, $currentSummary['total_balance']);
    }

    public function test_negative_balance_is_supported_and_calculated_accurately(): void
    {
        // Pengeluaran lebih besar dari pemasukan
        $this->user->transactions()->create([
            'category_id' => $this->incomeCategory->id,
            'type' => 'income',
            'amount' => 1000000.00,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $this->user->transactions()->create([
            'category_id' => $this->expenseCategory->id,
            'type' => 'expense',
            'amount' => 1500000.00,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $totalBalance = $this->financeService->getTotalBalance($this->user);
        $this->assertEquals(-500000.00, $totalBalance);

        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('-Rp 500.000');
        $response->assertSee('Defisit');
    }

    public function test_large_amounts_calculated_accurately(): void
    {
        // Nominal besar (ratusan juta)
        $this->user->transactions()->create([
            'category_id' => $this->incomeCategory->id,
            'type' => 'income',
            'amount' => 850000000.00,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $this->user->transactions()->create([
            'category_id' => $this->expenseCategory->id,
            'type' => 'expense',
            'amount' => 125000000.00,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $totalBalance = $this->financeService->getTotalBalance($this->user);
        $this->assertEquals(725000000.00, $totalBalance);
    }
}
