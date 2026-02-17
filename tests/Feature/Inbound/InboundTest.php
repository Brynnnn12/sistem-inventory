<?php

use App\Models\InboundTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseUser;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

// INDEX

test('super-admin bisa melihat daftar inbound', function () {
    $superAdmin = createSuperAdmin();

    InboundTransaction::factory()->count(3)->create();

    $response = actingAs($superAdmin)->get(route('inbound.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('inbound/index')
            ->has('inbounds.data', 3)
            ->has('warehouses')
            ->has('suppliers')
            ->has('products')
        );
});

test('admin hanya melihat inbound untuk gudangnya', function () {
    $admin = createAdmin();

    $warehouseA = Warehouse::factory()->create();
    $warehouseB = Warehouse::factory()->create();

    WarehouseUser::factory()->create([
        'user_id' => $admin->id,
        'warehouse_id' => $warehouseA->id,
    ]);

    InboundTransaction::factory()->create(['warehouse_id' => $warehouseA->id]);
    InboundTransaction::factory()->create(['warehouse_id' => $warehouseB->id]);

    $response = actingAs($admin)->get(route('inbound.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('inbound/index')
            ->has('inbounds.data', 1)
        );
});

// STORE

test('super-admin bisa membuat inbound dan memperbarui stok', function () {
    $superAdmin = createSuperAdmin();

    $warehouse = Warehouse::factory()->create();
    $supplier = Supplier::factory()->create();
    $product = Product::factory()->create();

    $payload = [
        'warehouse_id' => $warehouse->id,
        'supplier_id' => $supplier->id,
        'product_id' => $product->id,
        'quantity' => 5,
        'unit_price' => 10000,
        'received_date' => today()->format('Y-m-d'),
        'notes' => 'Inbound test',
    ];

    $response = actingAs($superAdmin)->post(route('inbound.store'), $payload);

    $response->assertRedirect(route('inbound.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('inbound_transactions', [
        'warehouse_id' => $warehouse->id,
        'supplier_id' => $supplier->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    // stok harus bertambah
    $this->assertDatabaseHas('stocks', [
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    $this->assertDatabaseHas('stock_histories', [
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'change_qty' => 5,
        'reference_type' => 'inbound',
    ]);
});

test('admin bisa membuat inbound tanpa mengirim warehouse_id (diisi otomatis)', function () {
    $admin = createAdmin();

    $warehouse = Warehouse::factory()->create();
    WarehouseUser::factory()->create([
        'user_id' => $admin->id,
        'warehouse_id' => $warehouse->id,
    ]);

    $supplier = Supplier::factory()->create();
    $product = Product::factory()->create();

    $payload = [
        'supplier_id' => $supplier->id,
        'product_id' => $product->id,
        'quantity' => 3,
        'unit_price' => 5000,
        'received_date' => today()->format('Y-m-d'),
    ];

    $response = actingAs($admin)->post(route('inbound.store'), $payload);

    $response->assertRedirect(route('inbound.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('inbound_transactions', [
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);
});

test('validasi store inbound: supplier, product, dan quantity wajib', function () {
    $superAdmin = createSuperAdmin();

    $response = actingAs($superAdmin)->post(route('inbound.store'), [
        // kosongkan payload
    ]);

    $response->assertSessionHasErrors(['warehouse_id', 'supplier_id', 'product_id', 'quantity', 'received_date']);
});
