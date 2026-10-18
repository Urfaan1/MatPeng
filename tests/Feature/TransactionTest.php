<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FinanceSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $incomeCategory;
    protected Category $expenseCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        (new FinanceSummaryService())->createDefaultCategories($this->user);

        $this->incomeCategory = $this->user->categories()->where('type', 'income')->first();
        $this->expenseCategory = $this->user->categories()->where('type', 'expense')->first();
    }

    public function test_income_transaction_saved_successfully(): void
    {
        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'income',
            'amount' => 5000000,
            'category_id' => $this->incomeCategory->id,
            'transaction_date' => '2026-10-18',
            'description' => 'Gaji Bulanan',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'category_id' => $this->incomeCategory->id,
            'type' => 'income',
            'amount' => 5000000.00,
            'description' => 'Gaji Bulanan',
        ]);
    }

    public function test_expense_transaction_saved_successfully(): void
    {
        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'expense',
            'amount' => 1750000,
            'category_id' => $this->expenseCategory->id,
            'transaction_date' => '2026-10-18',
            'description' => 'Belanja Supermarket',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'category_id' => $this->expenseCategory->id,
            'type' => 'expense',
            'amount' => 1750000.00,
            'description' => 'Belanja Supermarket',
        ]);
    }

    public function test_zero_or_negative_amount_rejected(): void
    {
        // Uji nominal 0
        $responseZero = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'income',
            'amount' => 0,
            'category_id' => $this->incomeCategory->id,
            'transaction_date' => '2026-10-18',
        ]);
        $responseZero->assertSessionHasErrors('amount');

        // Uji nominal negatif
        $responseNegative = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'income',
            'amount' => -250000,
            'category_id' => $this->incomeCategory->id,
            'transaction_date' => '2026-10-18',
        ]);
        $responseNegative->assertSessionHasErrors('amount');
    }

    public function test_mismatched_category_type_rejected(): void
    {
        // Transaksi tipe income tetapi memilih kategori expense
        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'income',
            'amount' => 1000000,
            'category_id' => $this->expenseCategory->id,
            'transaction_date' => '2026-10-18',
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    public function test_other_users_category_rejected(): void
    {
        $otherUser = User::factory()->create();
        (new FinanceSummaryService())->createDefaultCategories($otherUser);
        $otherUserCategory = $otherUser->categories()->where('type', 'income')->first();

        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'type' => 'income',
            'amount' => 1000000,
            'category_id' => $otherUserCategory->id,
            'transaction_date' => '2026-10-18',
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    public function test_transaction_can_be_viewed_and_edited(): void
    {
        $tx = $this->user->transactions()->create([
            'category_id' => $this->incomeCategory->id,
            'type' => 'income',
            'amount' => 1000000,
            'transaction_date' => '2026-10-18',
            'description' => 'Pendapatan Awal',
        ]);

        // Cek halaman detail
        $viewResponse = $this->actingAs($this->user)->get(route('transactions.show', $tx->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Pendapatan Awal');

        // Cek halaman edit
        $editResponse = $this->actingAs($this->user)->get(route('transactions.edit', $tx->id));
        $editResponse->assertStatus(200);

        // Update data
        $updateResponse = $this->actingAs($this->user)->put(route('transactions.update', $tx->id), [
            'type' => 'income',
            'amount' => 1250000,
            'category_id' => $this->incomeCategory->id,
            'transaction_date' => '2026-10-18',
            'description' => 'Pendapatan Direvisi',
        ]);

        $updateResponse->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'id' => $tx->id,
            'amount' => 1250000.00,
            'description' => 'Pendapatan Direvisi',
        ]);
    }

    public function test_transaction_can_be_deleted(): void
    {
        $tx = $this->user->transactions()->create([
            'category_id' => $this->expenseCategory->id,
            'type' => 'expense',
            'amount' => 300000,
            'transaction_date' => '2026-10-18',
            'description' => 'Hapus Saya',
        ]);

        $response = $this->actingAs($this->user)->delete(route('transactions.destroy', $tx->id));

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseMissing('transactions', ['id' => $tx->id]);
    }

    public function test_user_cannot_access_or_modify_other_users_transaction(): void
    {
        $otherUser = User::factory()->create();
        (new FinanceSummaryService())->createDefaultCategories($otherUser);
        $otherCategory = $otherUser->categories()->where('type', 'expense')->first();

        $otherTx = $otherUser->transactions()->create([
            'category_id' => $otherCategory->id,
            'type' => 'expense',
            'amount' => 500000,
            'transaction_date' => '2026-10-18',
            'description' => 'Data Rahasia User B',
        ]);

        // Coba lihat -> 403
        $this->actingAs($this->user)->get(route('transactions.show', $otherTx->id))->assertStatus(403);

        // Coba edit form -> 403
        $this->actingAs($this->user)->get(route('transactions.edit', $otherTx->id))->assertStatus(403);

        // Coba update -> 403
        $this->actingAs($this->user)->put(route('transactions.update', $otherTx->id), [
            'type' => 'expense',
            'amount' => 1000,
            'category_id' => $this->expenseCategory->id,
            'transaction_date' => '2026-10-18',
        ])->assertStatus(403);

        // Coba hapus -> 403
        $this->actingAs($this->user)->delete(route('transactions.destroy', $otherTx->id))->assertStatus(403);
    }
}
