<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Unit disesuaikan dengan produk Telur, Minyak, Beras, dan Susu
        $units = ['Kg', 'Liter', 'Karton', 'Pack', 'Karung', 'Pouch', 'Pcs', 'Tray'];

        $products = [
            // Telur
            'Telur Ayam Negeri 1 Kg',
            'Telur Ayam Kampung (Isi 10)',
            'Telur Bebek 1 Kg',
            'Telur Puyuh 500g',

            // Minyak
            'Minyak Goreng Bimoli Pouch 2L',
            'Minyak Goreng Sunco 1L',
            'Minyak Goreng Filma Jerigen 5L',
            'Minyak Goreng Curah 1 Kg',

            // Beras
            'Beras Premium 5 Kg',
            'Beras Rojolele 10 Kg',
            'Beras Pandan Wangi 5 Kg',
            'Beras Merah 1 Kg',

            // Susu
            'Susu UHT Full Cream 1L',
            'Susu Bubuk Dancow 400g',
            'Susu Kental Manis Frisian Flag 370g',
            'Susu Beruang Bear Brand 189ml',
        ];

        return [
            'code' => 'PRD-'.fake()->unique()->numberBetween(1000, 9999),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'name' => fake()->randomElement($products),
            'unit' => fake()->randomElement($units),
            'min_stock' => fake()->numberBetween(5, 20),
            'max_stock' => fake()->numberBetween(50, 200),
            // Harga dinaikkan agar lebih masuk akal untuk Beras 5kg atau Minyak 2L
            'price' => fake()->numberBetween(15000, 150000),
            'cost' => fake()->numberBetween(10000, 130000),
            'description' => fake()->optional(0.7)->sentence(),
            'is_active' => fake()->boolean(90), // 90% chance of being active
        ];
    }
}
