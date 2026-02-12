<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $suppliers = [
            // Distributor Makanan
            'PT. Indofood Sukses Makmur',
            'PT. Wings Food',
            'PT. Mayora Indah',
            'PT. Unilever Indonesia',
            'PT. Nestle Indonesia',
            'PT. Kalbe Farma',
            'PT. Sari Husada',

            // Distributor Minuman
            'PT. Coca-Cola Amatil Indonesia',
            'PT. Tirta Investama',
            'PT. Frisian Flag Indonesia',
            'PT. Danone Indonesia',

            // Distributor Bahan Pokok
            'PT. Gudang Garam',
            'PT. Indofood CBP Sukses Makmur',
            'PT. Salim Ivomas Pratama',
            'PT. Bina Karya Prima',

            // Distributor Produk Bayi
            'PT. Johnson & Johnson Indonesia',
            'PT. Kimberly-Clark Indonesia',
            'PT. P&G Indonesia',

            // Distributor Produk Rumah Tangga
            'PT. Reckitt Benckiser Indonesia',
            'PT. S. C. Johnson & Son Indonesia',
            'PT. Henkel Indonesia',
        ];

        $contactPersons = [
            'Budi Santoso',
            'Siti Aminah',
            'Ahmad Rahman',
            'Dewi Lestari',
            'Rudi Hartono',
            'Maya Sari',
            'Agus Priyanto',
            'Linda Kusuma',
            'Hendra Wijaya',
            'Rina Purnama',
        ];

        return [
            'code' => 'SUP-' . fake()->unique()->numberBetween(1000, 9999),
            'name' => fake()->randomElement($suppliers),
            'contact_person' => fake()->randomElement($contactPersons),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'tax_id' => 'NPWP-' . fake()->unique()->numberBetween(1000000000000000, 9999999999999999),
            'is_active' => fake()->boolean(95), // 95% chance of being active
        ];
    }
}
