<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Opname>
 */
class OpnameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $systemQty = $this->faker->randomFloat(2, 10, 500);
        $physicalQty = $systemQty + $this->faker->randomFloat(2, -20, 20);
        $differenceQty = abs($systemQty - $physicalQty);

        $differenceType = match(true) {
            $physicalQty > $systemQty => 'lebih',
            $physicalQty < $systemQty => 'kurang',
            default => 'sama'
        };

        return [
            'code' => 'OPN-' . $this->faker->unique()->numberBetween(100000, 999999),
            'warehouse_id' => Warehouse::factory(),
            'product_id' => Product::factory(),
            'system_qty' => $systemQty,
            'physical_qty' => $physicalQty,
            'difference_qty' => $differenceQty,
            'difference_type' => $differenceType,
            'notes' => $this->faker->optional(0.7)->sentence(),
            'opname_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the opname found a shortage.
     */
    public function shortage(): static
    {
        return $this->state(function (array $attributes) {
            $systemQty = $attributes['system_qty'];
            $shortage = $this->faker->randomFloat(2, 1, 50);
            $physicalQty = $systemQty - $shortage;

            return [
                'physical_qty' => $physicalQty,
                'difference_qty' => $shortage,
                'difference_type' => 'kurang',
            ];
        });
    }

    /**
     * Indicate that the opname found a surplus.
     */
    public function surplus(): static
    {
        return $this->state(function (array $attributes) {
            $systemQty = $attributes['system_qty'];
            $surplus = $this->faker->randomFloat(2, 1, 50);
            $physicalQty = $systemQty + $surplus;

            return [
                'physical_qty' => $physicalQty,
                'difference_qty' => $surplus,
                'difference_type' => 'lebih',
            ];
        });
    }

    /**
     * Indicate that the opname matched the system quantity.
     */
    public function accurate(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'physical_qty' => $attributes['system_qty'],
                'difference_qty' => 0,
                'difference_type' => 'sama',
            ];
        });
    }
}
