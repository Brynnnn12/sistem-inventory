<?php

namespace Database\Factories;

use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockHistory>
 */
class StockHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $changeType = $this->faker->randomElement(['in', 'out', 'adjustment']);
        $quantityChange = $this->faker->randomFloat(2, 1, 100);
        $quantityBefore = $this->faker->randomFloat(2, 0, 500);
        $quantityAfter = $changeType === 'in' ? $quantityBefore + $quantityChange : $quantityBefore - $quantityChange;

        return [
            'stock_id' => Stock::factory(),
            'reference_type' => $this->faker->randomElement([
                'inbound_transaction',
                'outbound_transaction',
                'stock_mutation',
                'opname',
                'manual_adjustment'
            ]),
            'reference_id' => $this->faker->numberBetween(1, 1000),
            'change_type' => $changeType,
            'quantity_before' => $quantityBefore,
            'quantity_change' => $quantityChange,
            'quantity_after' => $quantityAfter,
            'notes' => $this->faker->optional(0.7)->sentence(),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the history is for inbound transaction.
     */
    public function inbound(): static
    {
        return $this->state(fn (array $attributes) => [
            'reference_type' => 'inbound_transaction',
            'change_type' => 'in',
        ]);
    }

    /**
     * Indicate that the history is for outbound transaction.
     */
    public function outbound(): static
    {
        return $this->state(fn (array $attributes) => [
            'reference_type' => 'outbound_transaction',
            'change_type' => 'out',
        ]);
    }

    /**
     * Indicate that the history is for stock adjustment.
     */
    public function adjustment(): static
    {
        return $this->state(fn (array $attributes) => [
            'reference_type' => 'manual_adjustment',
            'change_type' => 'adjustment',
        ]);
    }
}
