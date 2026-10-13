<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index', ['categories' => Category::withCount('products')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:categories,name']);
        Category::create($data);

        return back()->with('status', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:categories,name,'.$category->id]);
        $category->update($data);

        return back()->with('status', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        $category->delete(); // produk terkait: category_id -> NULL

        return back()->with('status', 'Kategori dihapus.');
    }
}
