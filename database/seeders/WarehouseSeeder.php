<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific warehouses
        Warehouse::create([
            'code' => 'WHS-001',
            'name' => 'Gudang Pusat Jakarta',
            'address' => 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta 10220',
            'phone' => '021-12345678',
            'is_active' => true,
        ]);

        Warehouse::create([
            'code' => 'WHS-002',
            'name' => 'Gudang Cabang Bandung',
            'address' => 'Jl. Asia Afrika No. 456, Bandung, Jawa Barat 40111',
            'phone' => '022-87654321',
            'is_active' => true,
        ]);

        Warehouse::create([
            'code' => 'WHS-003',
            'name' => 'Gudang Cabang Surabaya',
            'address' => 'Jl. Tunjungan No. 789, Surabaya, Jawa Timur 60275',
            'phone' => '031-11223344',
            'is_active' => true,
        ]);

        // Create additional random warehouses
        Warehouse::factory(2)->create();
    }
}
