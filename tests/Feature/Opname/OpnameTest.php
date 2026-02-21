<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

use App\Models\Opname;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;

test('super-admin bisa membuat opname', function () {
    $superAdmin = createSuperAdmin();

    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();

    // existing stock
    $stock = Stock::factory()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'quantity' => 10,
    ]);

    $data = [
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'physical_qty' => 15,
        'opname_date' => today()->format('Y-m-d'),
        'notes' => 'Opname test',
    ];

    $response = actingAs($superAdmin)->post(route('opname.store'), $data);

    $response->assertRedirect(route('opname.index'))
        ->assertSessionHas('success', 'Opname berhasil dibuat.');

    $this->assertDatabaseHas('opnames', [
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'system_qty' => 10,
        'physical_qty' => 15,
        'difference_qty' => 5,
        'difference_type' => 'lebih',
        'status' => 'draft',
    ]);
});

test('gagal membuat opname jika sudah ada untuk tanggal yang sama', function () {
    $superAdmin = createSuperAdmin();

    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();

    Opname::factory()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'opname_date' => today()->format('Y-m-d'),
    ]);

    $data = [
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'physical_qty' => 5,
        'opname_date' => today()->format('Y-m-d'),
    ];

    $response = actingAs($superAdmin)->post(route('opname.store'), $data);

    $response->assertSessionHasErrors([
        'product_id' => 'Opname untuk produk ini pada tanggal yang sama sudah ada.',
    ]);

    // only one opname exists
    $this->assertDatabaseCount('opnames', 1);
});

test('menangani exception dari CreateOpnameAction dan menampilkan flash error', function () {
    $superAdmin = createSuperAdmin();

    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();

    // Bind a fake action that throws (use Mockery so the instance matches the type-hint)
    $mock = \Mockery::mock(\App\Actions\Opname\CreateOpnameAction::class);
    $mock->shouldReceive('execute')->andThrow(new \Exception('Boom'));
    app()->instance(\App\Actions\Opname\CreateOpnameAction::class, $mock);

    $data = [
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'physical_qty' => 5,
        'opname_date' => today()->format('Y-m-d'),
    ];

    $response = actingAs($superAdmin)->post(route('opname.store'), $data);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Gagal membuat opname: Boom');
});

// ============================================
// INDEX - role-based access
// ============================================

test('super-admin bisa melihat daftar opname', function () {
    $superAdmin = createSuperAdmin();

    $w1 = Warehouse::factory()->create();
    $w2 = Warehouse::factory()->create();

    \App\Models\Opname::factory()->create(['warehouse_id' => $w1->id]);
    \App\Models\Opname::factory()->create(['warehouse_id' => $w2->id]);

    $response = actingAs($superAdmin)->get(route('opname.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('opname/index')
            ->has('opnames.data', 2)
            ->has('warehouses')
            ->has('products')
            ->has('stocks')
        );
});

test('admin hanya melihat opname untuk gudangnya', function () {
    $admin = createAdmin();

    $w1 = Warehouse::factory()->create();
    $w2 = Warehouse::factory()->create();

    \App\Models\Opname::factory()->create(['warehouse_id' => $w1->id]);
    \App\Models\Opname::factory()->create(['warehouse_id' => $w2->id]);

    \App\Models\WarehouseUser::factory()->create(['user_id' => $admin->id, 'warehouse_id' => $w1->id]);

    $response = actingAs($admin)->get(route('opname.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('opname/index')
            ->has('opnames.data', 1)
        );
});

test('viewer tidak bisa melihat daftar opname', function () {
    $viewer = createViewer();

    actingAs($viewer)->get(route('opname.index'))->assertForbidden();
});

// ============================================
// APPROVE
// ============================================

test('super-admin bisa mengapprove opname dan stok disesuaikan', function () {
    $superAdmin = createSuperAdmin();

    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();

    // existing stock
    Stock::factory()->create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id, 'quantity' => 10]);

    // opname mencatat surplus 5 (physical 15)
    $opname = Opname::factory()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'system_qty' => 10,
        'physical_qty' => 15,
        'difference_qty' => 5,
        'difference_type' => 'lebih',
        'status' => 'draft',
    ]);

    $response = actingAs($superAdmin)->post(route('opname.approve', $opname));

    $response->assertRedirect(route('opname.index'))
        ->assertSessionHas('success', 'Opname berhasil diapprove dan stok disesuaikan.');

    $opname->refresh();
    expect($opname->status)->toBe('approved');

    // stok harus bertambah 5
    $this->assertDatabaseHas('stocks', ['warehouse_id' => $warehouse->id, 'product_id' => $product->id, 'quantity' => 15]);

    // ada stock_history tipe 'adjustment'
    $this->assertDatabaseHas('stock_histories', ['reference_type' => 'adjustment', 'reference_id' => $opname->id]);
});

test('admin di gudang bisa mengapprove opname', function () {
    $admin = createAdmin();

    $warehouse = Warehouse::factory()->create();
    Warehouse::factory()->create();
    \App\Models\WarehouseUser::factory()->create(['user_id' => $admin->id, 'warehouse_id' => $warehouse->id]);

    $product = Product::factory()->create();

    Stock::factory()->create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id, 'quantity' => 8]);

    $opname = Opname::factory()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'system_qty' => 8,
        'physical_qty' => 5,
        'difference_qty' => 3,
        'difference_type' => 'kurang',
        'status' => 'draft',
    ]);

    $response = actingAs($admin)->post(route('opname.approve', $opname));

    $response->assertRedirect(route('opname.index'))
        ->assertSessionHas('success', 'Opname berhasil diapprove dan stok disesuaikan.');

    $this->assertDatabaseHas('stocks', ['warehouse_id' => $warehouse->id, 'product_id' => $product->id, 'quantity' => 5]);
});

test('viewer tidak bisa mengapprove opname', function () {
    $viewer = createViewer();

    $opname = Opname::factory()->create();

    actingAs($viewer)->post(route('opname.approve', $opname))->assertForbidden();
});

test('tidak bisa mengapprove opname yang sudah diapprove', function () {
    $superAdmin = createSuperAdmin();

    $opname = Opname::factory()->create(['status' => 'approved']);

    $response = actingAs($superAdmin)->post(route('opname.approve', $opname));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});
