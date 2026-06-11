<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Makanan', 'Minuman', 'Bahan Pokok', 'Peralatan', 'Elektronik',
                'Kebersihan', 'ATK', 'Obat-obatan', 'Bumbu Dapur', 'Snack',
                'Rokok', 'Pakaian', 'Otomotif', 'Pertanian', 'Perikanan',
                'Kerajinan', 'Mainan', 'Olahraga', 'Kesehatan', 'Buku',
            ]),
        ];
    }
}
