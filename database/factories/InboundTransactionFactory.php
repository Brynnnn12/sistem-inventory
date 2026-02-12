<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InboundTransaction>
 */
class InboundTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 10, 500);
        $receivedQty = $quantity - $this->faker->randomFloat(2, 0, 5);
        $unitPrice = $this->faker->randomFloat(2, 1000, 50000);

        return [
            'code' => 'INB-' . $this->faker->unique()->numberBetween(100000, 999999),
            'supplier_id' => Supplier::factory(),
            'warehouse_id' => Warehouse::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'received_qty' => $receivedQty,
            'damaged_qty' => $quantity - $receivedQty,
            'unit_price' => $unitPrice,
            'total_price' => $receivedQty * $unitPrice,
            'receipt_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'notes' => $this->faker->optional(0.6)->sentence(),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the transaction has damaged goods.
     */
    public function withDamage(): static
    {
        return $this->state(fn (array $attributes) => [
            'damaged_qty' => $this->faker->randomFloat(2, 1, 10),
        ]);
    }

    /**
     * Indicate that the transaction is complete (no damage).
     */
    public function complete(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'received_qty' => $attributes['quantity'],
                'damaged_qty' => 0,
                'total_price' => $attributes['quantity'] * $attributes['unit_price'],
            ];
        });
    }
}
