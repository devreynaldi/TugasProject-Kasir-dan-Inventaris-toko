<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * @param  array<int, array{product_id:int, qty:int}>  $items
     */
    public function process(User $cashier, array $items, int $paid, string $method = 'cash'): Sale
    {
        // Gabungkan baris produk yang sama
        $qtyById = collect($items)->groupBy('product_id')->map(fn ($rows) => (int) $rows->sum('qty'));

        return DB::transaction(function () use ($cashier, $qtyById, $paid, $method) {
            // Kunci baris produk agar dua kasir tidak menjual stok yang sama
            $products = Product::active()->whereIn('id', $qtyById->keys())->lockForUpdate()->get()->keyBy('id');

            $total = 0;
            $lines = [];

            foreach ($qtyById as $id => $qty) {
                $product = $products->get($id);

                if (! $product) {
                    throw ValidationException::withMessages(['items' => 'Ada produk yang tidak ditemukan atau nonaktif.']);
                }
                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup (tersisa {$product->stock}).",
                    ]);
                }

                $subtotal = $product->price * $qty;
                $total += $subtotal;
                $lines[] = compact('product', 'qty', 'subtotal');
            }

            if ($method === 'cash' && $paid < $total) {
                throw ValidationException::withMessages(['paid' => 'Uang diterima kurang dari total belanja.']);
            }
            if ($method !== 'cash') {
                $paid = $total; // non-tunai dibayar pas
            }

            $sale = Sale::create([
                'invoice_no' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'user_id' => $cashier->id,
                'total' => $total,
                'paid' => $paid,
                'change_amount' => max(0, $paid - $total),
                'payment_method' => $method,
            ]);

            foreach ($lines as ['product' => $product, 'qty' => $qty, 'subtotal' => $subtotal]) {
                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $qty);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $cashier->id,
                    'type' => 'out',
                    'qty' => -$qty,
                    'note' => $sale->invoice_no,
                ]);
            }

            return $sale;
        });
    }
}
