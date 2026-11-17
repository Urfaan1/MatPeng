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
        $pendingUsersCount = User::where('is_admin', false)->where('is_active', false)->count();
        $activeUsersCount = User::where('is_admin', false)->where('is_active', true)->count();
        $totalTransactions = Transaction::count();
        $totalCategories = Category::count();

        $totalIncome = (float) Transaction::where('type', 'income')->sum('amount');
        $totalExpense = (float) Transaction::where('type', 'expense')->sum('amount');

        // Pencarian dan filter status
        $search = $request->query('search');
        $filter = $request->query('filter', 'all'); // all, pending, active, admin

        $usersQuery = User::withCount(['transactions', 'categories'])
            ->latest();

        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($filter === 'pending') {
            $usersQuery->where('is_admin', false)->where('is_active', false);
        } elseif ($filter === 'active') {
            $usersQuery->where('is_admin', false)->where('is_active', true);
        } elseif ($filter === 'admin') {
            $usersQuery->where('is_admin', true);
        }

        $users = $usersQuery->paginate(15)->withQueryString();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAdmins',
            'pendingUsersCount',
            'activeUsersCount',
            'totalTransactions',
            'totalCategories',
            'totalIncome',
            'totalExpense',
            'users',
            'search',
            'filter'
        ));
    }

    /**
     * Aktifkan atau nonaktifkan akun pengguna (konfirmasi admin).
     */
    public function toggleActive(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat mengubah status aktif akun Anda sendiri.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $action = $user->is_active ? 'berhasil disetujui & diaktifkan' : 'telah dinonaktifkan';

        return back()->with('success', "Akun pengguna {$user->name} {$action}.");
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

        // Jika dijadikan admin, otomatis diaktifkan juga
        if ($user->is_admin) {
            $user->is_active = true;
        }

        $user->save();

        $status = $user->is_admin ? 'dijadikan admin' : 'diubah menjadi pengguna biasa';

        return back()->with('success', "Status user {$user->name} berhasil {$status}.");
    }
}
