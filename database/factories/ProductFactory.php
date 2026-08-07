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
        $units = ['Kg', 'Karton', 'Pack',  'Pcs', 'Ball'];

        $products = [
            // Telur
            'Telur Ayam 1 Kg',

            // Minyak
            'Minyak Goreng Bimoli',

            // Beras
            'Beras Premium 5 Kg',

            // Susu
            'Susu UHT Full Cream 1L',

            //Garam
            'Garam Man',
        ];

        return [
            'code' => 'PRD-'.fake()->unique()->numberBetween(1000, 9999),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'name' => fake()->randomElement($products),
            'unit' => fake()->randomElement($units),
            'min_stock' => fake()->numberBetween(5, 20),
            'max_stock' => fake()->numberBetween(50, 200),

            // Dikalikan 1000 agar hasilnya genap (kelipatan 1000)
            'price' => fake()->numberBetween(15, 150) * 1000,
            'cost' => fake()->numberBetween(10, 130) * 1000,

            'description' => fake()->optional(0.7)->sentence(),
            'is_active' => fake()->boolean(90), // 90% chance of being active
        ];
    }
}
