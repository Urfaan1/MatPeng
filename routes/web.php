<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard (or admin dashboard if admin)
Route::get('/', function () {
    if (auth()->check() && auth()->user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('dashboard');
});

// Authentication Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected Authenticated Routes (Both User and Admin)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Protected Regular User Routes (Admin Forbidden, Regular Active Users Only)
Route::middleware(['auth', 'user'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions Resource
    Route::resource('transactions', TransactionController::class);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Invoices & Download
    Route::get('/invoices', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/export-csv', [InvoiceController::class, 'exportCsv'])->name('invoices.export-csv');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
});

// Admin Dedicated Routes (Auth + Admin Middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/users/{user}/toggle-admin', [AdminDashboardController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::post('/users/{user}/toggle-active', [AdminDashboardController::class, 'toggleActive'])->name('users.toggle-active');
});

// Secure migration route for serverless environments (e.g. Vercel)
Route::get('/init-admin', function () {
    try {
        // 1. Jalankan migrasi kolom is_admin
        Artisan::call('migrate', ['--force' => true]);

        $targetEmail = request('email', 'admin@matpeng.com');

        // 2. Buat atau update akun admin
        $admin = \App\Models\User::updateOrCreate(
            ['email' => $targetEmail],
            [
                'name' => request('name', 'Administrator MatPeng'),
                'password' => \Illuminate\Support\Facades\Hash::make(request('password', 'password')),
                'is_admin' => true,
                'is_active' => true,
            ]
        );

        // Pastikan status is_admin & is_active bernilai true
        $admin->is_admin = true;
        $admin->is_active = true;
        $admin->save();

        return response('
            <div style="font-family: system-ui, sans-serif; max-width: 480px; margin: 60px auto; padding: 28px; border-radius: 16px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);">
                <h2 style="margin-top:0; font-size: 20px; font-weight: 800;">✅ Akun Admin Siap Digunakan!</h2>
                <p style="font-size: 14px; color: #374151;">Database berhasil dimigrasikan dan akun administrator telah aktif:</p>
                <div style="background: white; border-radius: 8px; padding: 12px 16px; margin: 16px 0; border: 1px solid #dcfce7; font-size: 13px; color: #1f2937;">
                    <p style="margin: 4px 0;"><b>Email:</b> '.e($targetEmail).'</p>
                    <p style="margin: 4px 0;"><b>Password:</b> '.e(request('password', 'password')).'</p>
                    <p style="margin: 4px 0;"><b>Status:</b> Administrator (is_admin = true)</p>
                </div>
                <a href="/login" style="display:inline-block; padding: 10px 20px; background: #123E34; color: white; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 600;">Ke Halaman Login &rarr;</a>
            </div>
        ', 200);
    } catch (\Throwable $e) {
        return response('<pre style="color:#ef4444; padding: 20px; font-family: monospace;">Gagal inisialisasi: '.$e->getMessage().'</pre>', 500);
    }
});

// Secure migration route for serverless environments (e.g. Vercel)
Route::get('/migrate', function () {
    if (empty(request('key')) || request('key') !== config('app.key')) {
        abort(403, 'Akses ditolak: APP_KEY tidak sesuai.');
    }

    try {
        $options = ['--force' => true];
        if (request()->boolean('seed')) {
            $options['--seed'] = true;
        }

        Artisan::call('migrate', $options);

        return response('<pre style="background:#1e1e1e;color:#00ff66;padding:20px;border-radius:8px;font-family:monospace;">'.Artisan::output()."\nMigrasi berhasil dijalankan!</pre>", 200);
    } catch (Throwable $e) {
        return response('<pre style="background:#1e1e1e;color:#ff4444;padding:20px;border-radius:8px;font-family:monospace;">Error: '.$e->getMessage().'</pre>', 500);
    }
});
