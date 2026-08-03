<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            // Supplier Beras
            'PT. Bulog (Persero)',
            'UD. Beras Makmur Sentosa',
            'CV. Padi Jaya',
            'Kilang Padi Sinar Tani',

            // Supplier Minyak Goreng
            'PT. Salim Ivomas Pratama',
            'PT. Musim Mas',
            'PT. Sinar Mas Agro',
            'CV. Distributor Minyak Curah',

            // Supplier Telur
            'Peternakan Unggas Sejahtera',
            'CV. Telur Nusantara',
            'Sentra Ayam Petelur',
            'Peternakan Sumber Rejeki',

            // Supplier Susu
            'PT. Frisian Flag Indonesia',
            'PT. Nestle Indonesia',
            'KUD Susu Segar',
            'PT. Cisarua Mountain Dairy',
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
            'code' => 'SUP-'.fake()->unique()->numberBetween(1000, 9999),
            'name' => fake()->randomElement($suppliers),
            'contact_person' => fake()->randomElement($contactPersons),
            'phone' => '628'.fake()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            // Menghasilkan 15 digit angka murni untuk NPWP
            'tax_id' => fake()->unique()->numerify('###############'),
            'is_active' => fake()->boolean(95), // 95% chance of being active
        ];
    }
}
