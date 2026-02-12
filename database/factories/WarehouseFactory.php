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
        $warehouseNames = [
            'Gudang Tegal',
            'Gudang Brebes',
            'Gudang Pemalang',
        ];

        $cities = [
            'Tegal' => [
                'Jl. Pahlawan No. 45, Tegal',
                'Jl. Ahmad Yani No. 123, Tegal',
                'Jl. Raya Slawi KM 3, Tegal',
            ],
            'Brebes' => [
                'Jl. Dr. Sutomo No. 78, Brebes',
                'Jl. Raya Jatibarang No. 234, Brebes',
                'Jl. Pemuda No. 56, Brebes',
            ],
            'Pemalang' => [
                'Jl. Jenderal Sudirman No. 89, Pemalang',
                'Jl. Raya Comal KM 2, Pemalang',
                'Jl. Kartini No. 67, Pemalang',
            ],
        ];

        $city = $this->faker->randomElement(array_keys($cities));
        $address = $this->faker->randomElement($cities[$city]);

        return [
            'code' => 'WHS-' . $this->faker->unique()->numberBetween(100, 999),
            'name' => $this->faker->unique()->randomElement($warehouseNames),
            'address' => $address.', '.$city.', Indonesia '.$this->faker->postcode(),
            'phone' => $this->faker->phoneNumber(),
            'is_active' => $this->faker->boolean(90), // 90% chance active
        ];
    }
}
