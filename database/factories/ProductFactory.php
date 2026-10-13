<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $price = fake()->numberBetween(2, 50) * 1000;

        return [
            'category_id' => Category::factory(),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####??')),
            'name' => fake()->unique()->words(2, true),
            'price' => $price,
            'cost' => (int) ($price * 0.7),
            'stock' => 20,
            'min_stock' => 5,
            'is_active' => true,
        ];
    }
}
