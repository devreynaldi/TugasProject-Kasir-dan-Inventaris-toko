<?php

namespace App\Http\Controllers;

use App\Models\Sale;

class SaleController extends Controller
{
    public function index()
    {
        $query = Sale::with('cashier')->latest();

        // Kasir hanya melihat transaksinya sendiri
        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return view('sales.index', ['sales' => $query->paginate(15)]);
    }

    public function show(Sale $sale)
    {
        abort_unless(auth()->user()->isAdmin() || $sale->user_id === auth()->id(), 403);

        return view('sales.show', ['sale' => $sale->load(['items', 'cashier'])]);
    }
}
