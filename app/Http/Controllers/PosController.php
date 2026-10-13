<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::active()->where('stock', '>', 0)->orderBy('name')
            ->get(['id', 'sku', 'name', 'price', 'stock']);

        return view('pos.index', compact('products'));
    }

    public function checkout(Request $request, CheckoutService $checkout)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
            'paid' => 'required|integer|min:0',
            'payment_method' => 'required|in:cash,transfer,qris',
        ]);

        $sale = $checkout->process($request->user(), $data['items'], $data['paid'], $data['payment_method']);

        return response()->json(['redirect' => route('sales.show', $sale)]);
    }
}
