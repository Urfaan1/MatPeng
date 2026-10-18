<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori milik pengguna yang sedang login.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $categories = $user->categories()
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Simpan kategori baru untuk pengguna.
     */
    public function store(CategoryRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $category = $user->categories()->create($request->validated());

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil ditambahkan.");
    }

    /**
     * Perbarui kategori milik pengguna.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak memperbarui kategori ini.');
        }

        $category->update($request->validated());

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    /**
     * Hapus kategori milik pengguna dengan proteksi keterkaitan transaksi.
     */
    public function destroy(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus kategori ini.');
        }

        // Cek keterkaitan data: jika sudah digunakan pada transaksi, tidak boleh dihapus
        if ($category->transactions()->exists()) {
            $count = $category->transactions()->count();
            return redirect()->route('categories.index')
                ->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena sedang digunakan oleh {$count} transaksi aktif. Silakan ubah kategori pada transaksi terkait terlebih dahulu.");
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$name}' berhasil dihapus.");
    }
}
