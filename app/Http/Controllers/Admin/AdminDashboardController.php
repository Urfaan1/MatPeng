<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard admin.
     */
    public function index(Request $request)
    {
        $totalUsers = User::count();
        $totalAdmins = User::where('is_admin', true)->count();
        $totalTransactions = Transaction::count();
        $totalCategories = Category::count();

        $totalIncome = (float) Transaction::where('type', 'income')->sum('amount');
        $totalExpense = (float) Transaction::where('type', 'expense')->sum('amount');

        // Pencarian dan daftar user
        $search = $request->query('search');
        $usersQuery = User::withCount(['transactions', 'categories'])
            ->latest();

        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->paginate(10)->withQueryString();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAdmins',
            'totalTransactions',
            'totalCategories',
            'totalIncome',
            'totalExpense',
            'users',
            'search'
        ));
    }

    /**
     * Ubah status admin seorang pengguna.
     */
    public function toggleAdmin(User $user)
    {
        // Cegah mencabut status admin diri sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat mengubah status admin akun Anda sendiri.');
        }

        $user->is_admin = ! $user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'dijadikan admin' : 'diubah menjadi pengguna biasa';

        return back()->with('success', "Status user {$user->name} berhasil {$status}.");
    }
}
