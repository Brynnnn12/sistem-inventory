<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'warehouse_id' => Warehouse::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->randomFloat(2, 10, 1000),
            'reserved_qty' => $this->faker->randomFloat(2, 0, 100),
            'last_updated' => now(),
            'updated_by' => 1,
        ];
    }

    /**
     * Indicate that the stock has low quantity.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $this->faker->randomFloat(2, 1, 10),
            'reserved_qty' => 0,
        ]);
    }

    /**
     * Indicate that the stock has reserved quantity.
     */
    public function withReserved(): static
    {
        return $this->state(fn (array $attributes) => [
            'reserved_qty' => $this->faker->randomFloat(2, 1, 50),
        ]);
    }
}
