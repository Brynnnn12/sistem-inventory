<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WarehouseUser>
 */
class WarehouseUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? \App\Models\User::factory(),
            'warehouse_id' => \App\Models\Warehouse::inRandomOrder()->first()?->id ?? \App\Models\Warehouse::factory(),
            'assigned_by' => \App\Models\User::inRandomOrder()->first()?->id,
            'assigned_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'is_primary' => fake()->boolean(80), // 80% chance of being primary
        ];
    }
}
