<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        if ($warehouses->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Create stock for each warehouse-product combination
        foreach ($warehouses as $warehouse) {
            foreach ($products->random(min(5, $products->count())) as $product) {
                Stock::factory()->create([
                    'warehouse_id' => $warehouse->id,
                    'product_id' => $product->id,
                ]);
            }
        }

        // Ensure we have some low stock items
        Stock::factory()->count(3)->lowStock()->create();

        // Ensure we have some items with reserved quantities
        Stock::factory()->count(5)->withReserved()->create();
    }
}
