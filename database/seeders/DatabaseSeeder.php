<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Akun Administrator Bawaan
        User::firstOrCreate(
            ['email' => 'admin@matpeng.com'],
            [
                'name' => 'Administrator MatPeng',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'is_active' => true,
            ]
        );

        // Akun Pengguna Biasa
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'is_admin' => false,
                'is_active' => true,
            ]
        );
    }
}
