<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $categories = $user ? $user->categories()->withCount('transactions')->get() : Category::all();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        // Akan diimplementasikan penuh pada Tahap 5
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, Category $category)
    {
        // Akan diimplementasikan penuh pada Tahap 5
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy(Category $category)
    {
        // Akan diimplementasikan penuh pada Tahap 5
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus');
    }
}
