<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test root redirects to dashboard.
     */
    public function test_root_redirects_to_dashboard(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test dashboard returns 200 and renders MatPeng title.
     */
    public function test_dashboard_returns_successful_response(): void
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('MatPeng');
        $response->assertSee('Total Saldo Terkini');
    }
}
