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
            // unique() dibiarkan, pastikan saat menjalankan factory jumlahnya
            // tidak melebihi total isi array di bawah ini agar tidak error.
            'name' => fake()->unique()->randomElement([
                'Beras',
                'Minyak Goreng',
                'Telur',
                'Susu',
                'Bahan Pokok',
                'Sembako',
                'Minuman',
                'Kebutuhan Dapur'
            ]),
        ];
    }
}
