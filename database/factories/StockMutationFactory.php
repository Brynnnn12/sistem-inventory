<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockMutation>
 */
class StockMutationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 10, 200);
        $receivedQty = $quantity - $this->faker->randomFloat(2, 0, 5);
        $status = $this->faker->randomElement(['dikirim', 'diterima', 'ditolak', 'selesai']);

        return [
            'code' => 'MUT-' . $this->faker->unique()->numberBetween(100000, 999999),
            'from_warehouse' => Warehouse::factory(),
            'to_warehouse' => Warehouse::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'received_qty' => $receivedQty,
            'damaged_qty' => $quantity - $receivedQty,
            'status' => $status,
            'sent_at' => $this->faker->dateTimeBetween('-7 days', '-1 day'),
            'received_at' => in_array($status, ['diterima', 'selesai']) ? $this->faker->dateTimeBetween('-1 day', 'now') : null,
            'created_by' => User::factory(),
            'received_by' => in_array($status, ['diterima', 'selesai']) ? User::factory() : null,
            'notes' => $this->faker->optional(0.6)->sentence(),
        ];
    }

    /**
     * Indicate that the mutation is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'dikirim',
            'received_at' => null,
            'received_by' => null,
        ]);
    }

    /**
     * Indicate that the mutation is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'selesai',
            'received_qty' => $attributes['quantity'],
            'damaged_qty' => 0,
        ]);
    }

    /**
     * Indicate that the mutation has damage.
     */
    public function withDamage(): static
    {
        return $this->state(fn (array $attributes) => [
            'damaged_qty' => $this->faker->randomFloat(2, 1, 10),
            'received_qty' => $attributes['quantity'] - $this->faker->randomFloat(2, 1, 10),
        ]);
    }
}
