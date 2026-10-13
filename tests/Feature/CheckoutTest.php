<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function cashier(): User
    {
        return User::factory()->create(['role' => 'kasir']);
    }

    public function test_checkout_creates_sale_and_reduces_stock(): void
    {
        $product = Product::factory()->create(['price' => 10000, 'stock' => 10]);

        $response = $this->actingAs($this->cashier())->postJson(route('pos.checkout'), [
            'items' => [['product_id' => $product->id, 'qty' => 3]],
            'paid' => 50000,
            'payment_method' => 'cash',
        ]);

        $response->assertOk()->assertJsonStructure(['redirect']);
        $this->assertDatabaseHas('sales', ['total' => 30000, 'paid' => 50000, 'change_amount' => 20000]);
        $this->assertDatabaseHas('sale_items', ['product_id' => $product->id, 'qty' => 3, 'subtotal' => 30000]);
        $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'type' => 'out', 'qty' => -3]);
        $this->assertSame(7, $product->fresh()->stock);
    }

    public function test_checkout_rejected_when_stock_is_insufficient(): void
    {
        $product = Product::factory()->create(['stock' => 2]);

        $this->actingAs($this->cashier())->postJson(route('pos.checkout'), [
            'items' => [['product_id' => $product->id, 'qty' => 5]],
            'paid' => 999999,
            'payment_method' => 'cash',
        ])->assertStatus(422)->assertJsonValidationErrors('items');

        $this->assertSame(0, Sale::count());
        $this->assertSame(2, $product->fresh()->stock);
    }

    public function test_checkout_rejected_when_cash_is_less_than_total(): void
    {
        $product = Product::factory()->create(['price' => 10000, 'stock' => 10]);

        $this->actingAs($this->cashier())->postJson(route('pos.checkout'), [
            'items' => [['product_id' => $product->id, 'qty' => 2]],
            'paid' => 5000,
            'payment_method' => 'cash',
        ])->assertStatus(422)->assertJsonValidationErrors('paid');

        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_duplicate_lines_are_merged_before_stock_check(): void
    {
        $product = Product::factory()->create(['price' => 1000, 'stock' => 3]);

        $this->actingAs($this->cashier())->postJson(route('pos.checkout'), [
            'items' => [['product_id' => $product->id, 'qty' => 2], ['product_id' => $product->id, 'qty' => 2]],
            'paid' => 10000,
            'payment_method' => 'cash',
        ])->assertStatus(422);
    }
}
