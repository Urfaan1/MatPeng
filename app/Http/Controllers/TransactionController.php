<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Tampilkan daftar riwayat transaksi pengguna dengan filter dan pencarian.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = $user->transactions()->with('category');

        // Filter berdasarkan jenis (income / expense)
        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        // Filter berdasarkan kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // Filter pencarian berdasarkan deskripsi
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . trim($request->search) . '%');
        }

        $transactions = $query->latest('transaction_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $categories = $user->categories()->orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    /**
     * Tampilkan formulir pencatatan transaksi baru.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $categories = $user->categories()->orderBy('name')->get();

        return view('transactions.create', compact('categories'));
    }

    /**
     * Simpan transaksi baru ke database MySQL.
     */
    public function store(StoreTransactionRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $transaction = $user->transactions()->create($request->validated());

        $typeLabel = $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran';

        return redirect()->route('transactions.index')
            ->with('success', "Transaksi {$typeLabel} berhasil disimpan.");
    }

    /**
     * Tampilkan rincian transaksi tertentu.
     */
    public function show(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses ke transaksi ini.');
        }

        $transaction->load('category');

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Tampilkan formulir edit transaksi.
     */
    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses ke transaksi ini.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $categories = $user->categories()->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    /**
     * Perbarui transaksi yang ada di database.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses ke transaksi ini.');
        }

        $transaction->update($request->validated());

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Hapus transaksi dari database.
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses ke transaksi ini.');
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
