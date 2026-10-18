<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->transactions()->with('category');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->latest('transaction_date')->latest('id')->paginate(15);
        $categories = $user->categories()->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $categories = $user->categories()->get();

        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Akan diimplementasikan penuh pada Tahap 3
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses transaksi ini.');
        }

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses transaksi ini.');
        }

        $user = Auth::user();
        $categories = $user->categories()->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses transaksi ini.');
        }

        // Akan diimplementasikan penuh pada Tahap 3
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus transaksi ini.');
        }

        // Akan diimplementasikan penuh pada Tahap 3
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus');
    }
}
