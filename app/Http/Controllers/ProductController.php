<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->search($request->query('q'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.form', ['product' => new Product(['min_stock' => 5, 'is_active' => true]), 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        Product::create($this->validated($request));

        return redirect()->route('products.index')->with('status', 'Produk ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('products.form', ['product' => $product, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return redirect()->route('products.index')->with('status', 'Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete(); // soft delete

        return back()->with('status', 'Produk dihapus.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product)],
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'cost' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'stock' => $product ? 'prohibited' : 'required|integer|min:0', // stok diubah lewat menu Stok
            'is_active' => 'sometimes|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
