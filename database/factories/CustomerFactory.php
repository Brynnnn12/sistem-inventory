<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customers = [
            // Toko Retail
            'Toko Sembako Makmur',
            'Minimarket Indah',
            'Warung Kelontong Jaya',
            'Supermarket Segar',
            'Toko Elektronik Maju',
            'Apotek Sehat',
            'Toko Pakaian Modern',

            // Restoran & Kafe
            'Restoran Padang Raya',
            'Kafe Kopi Kenangan',
            'Warung Makan Sari Rasa',
            'Bakery Cita Rasa',
            'Food Court Plaza',

            // Hotel & Penginapan
            'Hotel Bintang Lima',
            'Penginapan Melati',
            'Villa Indah Resort',
            'Guest House Permata',

            // Perusahaan
            'PT. Maju Bersama',
            'CV. Sukses Abadi',
            'UD. Makmur Sentosa',
            'PT. Berkah Jaya',
        ];

        $contactPersons = [
            'Ahmad Susanto',
            'Siti Nurhaliza',
            'Budi Santoso',
            'Maya Sari',
            'Rudi Hartono',
            'Linda Kusuma',
            'Agus Priyanto',
            'Dewi Lestari',
            'Hendra Wijaya',
            'Rina Purnama',
        ];

        return [
            'code' => 'CST-' . fake()->unique()->numberBetween(1000, 9999),
            'name' => fake()->randomElement($customers),
            'contact_person' => fake()->randomElement($contactPersons),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'is_active' => fake()->boolean(95), // 95% chance of being active
        ];
    }
}
