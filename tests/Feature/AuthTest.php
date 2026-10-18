<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_protected_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_guests_can_view_login_page(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Masuk ke MatPeng');
    }

    public function test_guests_can_view_register_page(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertSee('Mulai Bersama MatPeng');
    }

    public function test_user_can_register_and_automatically_receives_default_categories(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Budi Santoso', $user->name);

        // Verifikasi pembuatan kategori default (idempotent)
        $incomeCategories = $user->categories()->where('type', 'income')->pluck('name')->toArray();
        $expenseCategories = $user->categories()->where('type', 'expense')->pluck('name')->toArray();

        $this->assertContains('Gaji', $incomeCategories);
        $this->assertContains('Bonus', $incomeCategories);
        $this->assertContains('Makanan dan Minuman', $expenseCategories);
        $this->assertContains('Transportasi', $expenseCategories);
        $this->assertContains('Tagihan', $expenseCategories);
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'duplikat@example.com']);

        $response = $this->post(route('register'), [
            'name' => 'Pengguna Baru',
            'email' => 'duplikat@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'testuser@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'testuser@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_user_can_update_profile_name(): void
    {
        $user = User::factory()->create(['name' => 'Nama Lama']);

        $response = $this->actingAs($user)->put(route('settings.profile'), [
            'name' => 'Nama Baru',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru',
        ]);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $response = $this->actingAs($user)->put(route('settings.password'), [
            'current_password' => 'OldPassword123!',
            'new_password' => 'NewPassword123!',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
    }
}
