<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'WHS-'.$this->faker->unique()->numberBetween(100000, 999999),
            'name' => $this->faker->randomElement(['Gudang Brebes', 'Gudang Tegal', 'Gudang Pemalang']),
            'address' => $this->faker->address(),
            'phone' => $this->faker->numerify('08##########'),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
