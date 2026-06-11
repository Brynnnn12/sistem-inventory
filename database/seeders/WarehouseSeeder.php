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
        $warehouses = [
            ['code' => 'WHS-001', 'name' => 'Gudang Brebes', 'address' => 'Jl. Dr. Sutomo No. 78, Brebes, Jawa Tengah 52212', 'phone' => '0283-1234567'],
            ['code' => 'WHS-002', 'name' => 'Gudang Tegal', 'address' => 'Jl. Pahlawan No. 45, Tegal, Jawa Tengah 52111', 'phone' => '0283-7654321'],
            ['code' => 'WHS-003', 'name' => 'Gudang Pemalang', 'address' => 'Jl. Jenderal Sudirman No. 89, Pemalang, Jawa Tengah 52311', 'phone' => '0284-1122334'],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create([...$warehouse, 'is_active' => true]);
        }
    }
}
