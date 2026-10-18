<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

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

// Settings
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
