<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo — GANTI password sebelum production.
        User::create(['name' => 'Admin Toko', 'email' => 'admin@tokoku.test', 'password' => 'password', 'role' => 'admin']);
        User::create(['name' => 'Kasir 1', 'email' => 'kasir@tokoku.test', 'password' => 'password', 'role' => 'kasir']);

        $data = [
            'Minuman' => [['MNM-001', 'Air Mineral 600ml', 3500, 2500, 48], ['MNM-002', 'Teh Botol 350ml', 5000, 3600, 36], ['MNM-003', 'Kopi Sachet', 2000, 1400, 4]],
            'Makanan' => [['MKN-001', 'Mie Instan Goreng', 3500, 2800, 60], ['MKN-002', 'Roti Tawar', 16000, 12500, 12], ['MKN-003', 'Biskuit Kelapa', 9000, 6800, 3]],
            'Kebutuhan Rumah' => [['KBR-001', 'Sabun Cuci Piring 400ml', 12500, 9500, 20], ['KBR-002', 'Tisu Gulung', 8000, 5800, 25]],
        ];

        foreach ($data as $cat => $items) {
            $category = Category::create(['name' => $cat]);
            foreach ($items as [$sku, $name, $price, $cost, $stock]) {
                Product::create(compact('sku', 'name', 'price', 'cost', 'stock') + ['category_id' => $category->id]);
            }
        }
    }
}
