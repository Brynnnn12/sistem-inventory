<?php

use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;


test('super-admin bisa melihat daftar stock', function () {
    $superAdmin = createSuperAdmin();

    $stocks = Stock::factory()->count(3)->create();

    // buat satu history untuk memastikan relation `histories` ter-include
    StockHistory::create([
        'stock_id' => $stocks->first()->id,
        'warehouse_id' => $stocks->first()->warehouse_id,
        'product_id' => $stocks->first()->product_id,
        'previous_qty' => 10.00,
        'new_qty' => 15.00,
        'change_qty' => 5.00,
        'reference_type' => 'adjustment',
        'reference_id' => 1,
        'reference_code' => 'ADJ-1',
        'notes' => 'Adjustment test',
        'created_by' => createSuperAdmin()->id,
    ]);

    $response = actingAs($superAdmin)->get(route('stocks.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('stocks/index')
            ->has('stocks.data', 3)
            ->has('stocks.data.0', fn ($stock) => $stock
                ->has('id')
                ->has('warehouse')
                ->has('product')
                ->has('warehouse_id')
                ->has('product_id')
                ->has('quantity')
                ->has('reserved_qty')
                ->has('available_qty')
                ->has('last_updated')
                ->has('updated_by')
                ->has('created_at')
                ->has('updated_at')
                ->has('histories.0')
            )
        );
});


test('bisa mem-filter stock berdasarkan gudang dan produk', function () {
    $superAdmin = createSuperAdmin();

    $warehouseA = Warehouse::factory()->create();
    $warehouseB = Warehouse::factory()->create();

    $productA = Product::factory()->create();
    $productB = Product::factory()->create();

    Stock::factory()->create(['warehouse_id' => $warehouseA->id, 'product_id' => $productA->id]);
    Stock::factory()->create(['warehouse_id' => $warehouseA->id, 'product_id' => $productB->id]);
    Stock::factory()->create(['warehouse_id' => $warehouseB->id, 'product_id' => $productA->id]);

    // filter by warehouse A
    $response = actingAs($superAdmin)->get(route('stocks.index', ['warehouse_id' => $warehouseA->id]));
    $response->assertOk()->assertInertia(fn ($page) => $page->has('stocks.data', 2));

    // filter by product A
    $response = actingAs($superAdmin)->get(route('stocks.index', ['product_id' => $productA->id]));
    $response->assertOk()->assertInertia(fn ($page) => $page->has('stocks.data', 2));

    // filter by warehouse A + product B (single row)
    $response = actingAs($superAdmin)->get(route('stocks.index', ['warehouse_id' => $warehouseA->id, 'product_id' => $productB->id]));
    $response->assertOk()->assertInertia(fn ($page) => $page->has('stocks.data', 1));
});


test('super-admin bisa melihat daftar riwayat stok', function () {
    $superAdmin = createSuperAdmin();

    $stock = Stock::factory()->create();

    $user = createAdmin();

    StockHistory::create([
        'stock_id' => $stock->id,
        'warehouse_id' => $stock->warehouse_id,
        'product_id' => $stock->product_id,
        'previous_qty' => 5.00,
        'new_qty' => 10.00,
        'change_qty' => 5.00,
        'reference_type' => 'inbound',
        'reference_id' => 1,
        'reference_code' => 'INB-1',
        'notes' => 'Inbound test',
        'created_by' => $user->id,
    ]);

    StockHistory::create([
        'stock_id' => $stock->id,
        'warehouse_id' => $stock->warehouse_id,
        'product_id' => $stock->product_id,
        'previous_qty' => 10.00,
        'new_qty' => 7.00,
        'change_qty' => -3.00,
        'reference_type' => 'outbound',
        'reference_id' => 2,
        'reference_code' => 'OUT-2',
        'notes' => 'Outbound test',
        'created_by' => $user->id,
    ]);

    $response = actingAs($superAdmin)->get(route('stock-history.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('stock-history/index')
            ->has('stockHistories.data', 2)
            ->has('stockHistories.data.0')
        );
});


test('admin hanya melihat stock history untuk gudangnya sendiri', function () {
    $admin = createAdmin();

    $warehouseA = Warehouse::factory()->create();
    $warehouseB = Warehouse::factory()->create();

    // beri akses admin hanya ke warehouse A
    $admin->warehouses()->sync([$warehouseA->id]);

    $stockA = Stock::factory()->create(['warehouse_id' => $warehouseA->id]);
    $stockB = Stock::factory()->create(['warehouse_id' => $warehouseB->id]);

    StockHistory::create([
        'stock_id' => $stockA->id,
        'warehouse_id' => $stockA->warehouse_id,
        'product_id' => $stockA->product_id,
        'previous_qty' => 1,
        'new_qty' => 2,
        'change_qty' => 1,
        'reference_type' => 'adjustment',
        'reference_id' => 10,
        'reference_code' => 'ADJ-10',
        'notes' => 'For A',
        'created_by' => createViewer()->id,
    ]);

    StockHistory::create([
        'stock_id' => $stockB->id,
        'warehouse_id' => $stockB->warehouse_id,
        'product_id' => $stockB->product_id,
        'previous_qty' => 2,
        'new_qty' => 1,
        'change_qty' => -1,
        'reference_type' => 'adjustment',
        'reference_id' => 11,
        'reference_code' => 'ADJ-11',
        'notes' => 'For B',
        'created_by' => createViewer()->id,
    ]);

    $response = actingAs($admin)->get(route('stock-history.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('stock-history/index')
            ->has('stockHistories.data', 1)
        );
});
