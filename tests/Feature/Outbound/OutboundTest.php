<?php

use App\Models\Customer;
use App\Models\OutboundTransaction;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseUser;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

// INDEX

test('super-admin bisa melihat daftar outbound', function () {
    $superAdmin = createSuperAdmin();

    OutboundTransaction::factory()->count(3)->create();

    $response = actingAs($superAdmin)->get(route('outbound.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('outbound/index')
            ->has('outbounds.data', 3)
            ->has('warehouses')
            ->has('customers')
            ->has('products')
            ->has('stocks')
        );
});

test('admin hanya melihat outbound untuk gudangnya', function () {
    $admin = createAdmin();

    $warehouseA = Warehouse::factory()->create();
    $warehouseB = Warehouse::factory()->create();

    WarehouseUser::factory()->create([
        'user_id' => $admin->id,
        'warehouse_id' => $warehouseA->id,
    ]);

    OutboundTransaction::factory()->create(['warehouse_id' => $warehouseA->id]);
    OutboundTransaction::factory()->create(['warehouse_id' => $warehouseB->id]);

    $response = actingAs($admin)->get(route('outbound.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('outbound/index')
            ->has('outbounds.data', 1)
        );
});

// STORE

test('super-admin bisa membuat outbound dan mengurangi stok', function () {
    $superAdmin = createSuperAdmin();

    $warehouse = Warehouse::factory()->create();
    $customer = Customer::factory()->create();
    $product = Product::factory()->create();

    // existing stock
    Stock::factory()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'quantity' => 10,

    ]);

    $payload = [
        'warehouse_id' => $warehouse->id,
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'quantity' => 4,
        'unit_price' => 15000,
        'sale_date' => today()->format('Y-m-d'),
        'notes' => 'Outbound test',
    ];

    $response = actingAs($superAdmin)->post(route('outbound.store'), $payload);

    $response->assertRedirect(route('outbound.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('outbound_transactions', [
        'warehouse_id' => $warehouse->id,
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'quantity' => 4,
    ]);

    $this->assertDatabaseHas('stocks', [
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'quantity' => 6, // 10 - 4
    ]);

    $this->assertDatabaseHas('stock_histories', [
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'change_qty' => -4,
        'reference_type' => 'outbound',
    ]);
});

test('admin bisa membuat outbound tanpa mengirim warehouse_id (diisi otomatis)', function () {
    $admin = createAdmin();

    $warehouse = Warehouse::factory()->create();
    WarehouseUser::factory()->create([
        'user_id' => $admin->id,
        'warehouse_id' => $warehouse->id,
    ]);

    $customer = Customer::factory()->create();
    $product = Product::factory()->create();

    Stock::factory()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    $payload = [
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price' => 8000,
        'sale_date' => today()->format('Y-m-d'),
    ];

    $response = actingAs($admin)->post(route('outbound.store'), $payload);

    $response->assertRedirect(route('outbound.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('outbound_transactions', [
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

test('gagal membuat outbound jika stok tidak cukup', function () {
    $superAdmin = createSuperAdmin();

    $warehouse = Warehouse::factory()->create();
    $customer = Customer::factory()->create();
    $product = Product::factory()->create();

    // only 2 in stock
    Stock::factory()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $payload = [
        'warehouse_id' => $warehouse->id,
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'quantity' => 5, // request more than available
        'unit_price' => 10000,
        'sale_date' => today()->format('Y-m-d'),
    ];

    $response = actingAs($superAdmin)->post(route('outbound.store'), $payload);

    $response->assertSessionHasErrors(['quantity']);
});

test('validasi store outbound: customer, product, dan quantity wajib', function () {
    $superAdmin = createSuperAdmin();

    $response = actingAs($superAdmin)->post(route('outbound.store'), []);

    $response->assertSessionHasErrors(['warehouse_id', 'customer_id', 'product_id', 'quantity', 'sale_date']);
});
