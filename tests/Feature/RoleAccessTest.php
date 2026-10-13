<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('pos.index'))->assertRedirect(route('login'));
    }

    public function test_kasir_cannot_open_admin_pages(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);

        foreach (['products.index', 'categories.index', 'stock.index', 'reports.index'] as $route) {
            $this->actingAs($kasir)->get(route($route))->assertForbidden();
        }
    }

    public function test_kasir_can_open_pos_and_admin_can_open_products(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'kasir']))->get(route('pos.index'))->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('products.index'))->assertOk();
    }

    public function test_kasir_cannot_view_another_cashiers_receipt(): void
    {
        $owner = User::factory()->create(['role' => 'kasir']);
        $other = User::factory()->create(['role' => 'kasir']);
        $sale = \App\Models\Sale::create(['invoice_no' => 'INV-X', 'user_id' => $owner->id, 'total' => 1000, 'paid' => 1000]);

        $this->actingAs($other)->get(route('sales.show', $sale))->assertForbidden();
        $this->actingAs($owner)->get(route('sales.show', $sale))->assertOk();
    }
}
