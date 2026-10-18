<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_transactions_page_accessible(): void
    {
        $response = $this->get(route('transactions.index'));
        $response->assertStatus(200);
        $response->assertSee('Riwayat Transaksi');
    }

    public function test_create_transaction_page_accessible(): void
    {
        $response = $this->get(route('transactions.create'));
        $response->assertStatus(200);
        $response->assertSee('Catat Transaksi');
    }

    public function test_categories_page_accessible(): void
    {
        $response = $this->get(route('categories.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Kategori');
    }

    public function test_reports_page_accessible(): void
    {
        $response = $this->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Keuangan');
    }

    public function test_settings_page_accessible(): void
    {
        $response = $this->get(route('settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Akun');
    }
}
