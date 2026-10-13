<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockController extends Controller
{
    public function index()
    {
        return view('stock.index', [
            'products' => Product::active()->orderBy('name')->get(),
            'movements' => StockMovement::with(['product', 'user'])->latest()->limit(20)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,adjust',
            'qty' => 'required|integer|not_in:0',
            'note' => 'nullable|string|max:255',
        ]);

        if ($data['type'] === 'in' && $data['qty'] < 0) {
            throw ValidationException::withMessages(['qty' => 'Stok masuk harus bernilai positif.']);
        }

        DB::transaction(function () use ($data, $request) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            if ($product->stock + $data['qty'] < 0) {
                throw ValidationException::withMessages(['qty' => 'Penyesuaian membuat stok menjadi negatif.']);
            }

            $product->increment('stock', $data['qty']);

            StockMovement::create($data + ['user_id' => $request->user()->id]);
        });

        return back()->with('status', 'Stok diperbarui.');
    }
}
