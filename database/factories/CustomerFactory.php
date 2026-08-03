<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Variasi Koperasi dan Dapur MBG
        $entities = [
            'Koperasi Pusat',
            'Koperasi Karyawan',
            'Koperasi Unit 1',
            'Koperasi Unit 2',
            'Dapur MBG Pusat',
            'Dapur MBG Wilayah Utara',
            'Dapur MBG Wilayah Selatan',
            'Dapur MBG Sektor 1',
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
        ];

        return [
            // Ubah prefix 'CST-' menjadi 'SPL-' jika ini diubah menjadi SupplierFactory
            'code' => 'CST-'.fake()->unique()->numberBetween(1000, 9999),
            'name' => fake()->randomElement($entities),
            'contact_person' => fake()->randomElement($contactPersons),
            'phone' => '628'.fake()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'is_active' => fake()->boolean(95), // 95% chance of being active
        ];
    }
}
