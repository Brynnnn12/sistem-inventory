<?php

use App\Models\Opname;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;

use function Pest\Laravel\actingAs;

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

test('admin tidak bisa mengapprove opname (hanya super-admin)', function () {
    $admin = createAdmin();

    $warehouse = Warehouse::factory()->create();
    \App\Models\WarehouseUser::factory()->create(['user_id' => $admin->id, 'warehouse_id' => $warehouse->id]);

    $opname = Opname::factory()->create(['warehouse_id' => $warehouse->id, 'status' => 'draft']);

    actingAs($admin)->post(route('opname.approve', $opname))->assertForbidden();
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

// ============================================
// REJECT
// ============================================

test('super-admin bisa menolak opname', function () {
    $superAdmin = createSuperAdmin();

    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();

    $opname = Opname::factory()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'status' => 'draft',
    ]);

    $response = actingAs($superAdmin)->post(route('opname.reject', $opname));

    $response->assertRedirect(route('opname.index'))
        ->assertSessionHas('success', 'Opname berhasil ditolak.');

    $opname->refresh();
    expect($opname->status)->toBe('rejected');
});

test('admin tidak bisa menolak opname (hanya super-admin)', function () {
    $admin = createAdmin();
    $warehouse = Warehouse::factory()->create();
    \App\Models\WarehouseUser::factory()->create(['user_id' => $admin->id, 'warehouse_id' => $warehouse->id]);

    $opname = Opname::factory()->create(['warehouse_id' => $warehouse->id, 'status' => 'draft']);

    actingAs($admin)->post(route('opname.reject', $opname))->assertForbidden();
});

test('viewer tidak bisa menolak opname', function () {
    $viewer = createViewer();
    $opname = Opname::factory()->create();

    actingAs($viewer)->post(route('opname.reject', $opname))->assertForbidden();
});

test('tidak bisa menolak opname yang sudah diapprove', function () {
    $superAdmin = createSuperAdmin();
    $opname = Opname::factory()->create(['status' => 'approved']);

    $response = actingAs($superAdmin)->post(route('opname.reject', $opname));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('tidak bisa menolak opname yang sudah ditolak', function () {
    $superAdmin = createSuperAdmin();
    $opname = Opname::factory()->create(['status' => 'rejected']);

    $response = actingAs($superAdmin)->post(route('opname.reject', $opname));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

// ============================================
// DELETE
// ============================================

test('tidak bisa menghapus opname draft (hanya rejected)', function () {
    $superAdmin = createSuperAdmin();
    $opname = Opname::factory()->create(['status' => 'draft']);

    $response = actingAs($superAdmin)->delete(route('opname.destroy', $opname));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('super-admin bisa menghapus opname rejected', function () {
    $superAdmin = createSuperAdmin();
    $opname = Opname::factory()->create(['status' => 'rejected']);

    $response = actingAs($superAdmin)->delete(route('opname.destroy', $opname));

    $response->assertRedirect(route('opname.index'))
        ->assertSessionHas('success', 'Opname berhasil dihapus.');

    $this->assertDatabaseMissing('opnames', ['id' => $opname->id]);
});

test('tidak bisa menghapus opname yang sudah diapprove', function () {
    $superAdmin = createSuperAdmin();
    $opname = Opname::factory()->create(['status' => 'approved']);

    $response = actingAs($superAdmin)->delete(route('opname.destroy', $opname));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('admin tidak bisa menghapus opname (hanya super-admin)', function () {
    $admin = createAdmin();
    $warehouse = Warehouse::factory()->create();
    \App\Models\WarehouseUser::factory()->create(['user_id' => $admin->id, 'warehouse_id' => $warehouse->id]);

    $opname = Opname::factory()->create(['warehouse_id' => $warehouse->id, 'status' => 'draft']);

    actingAs($admin)->delete(route('opname.destroy', $opname))->assertForbidden();
});

// ============================================
// DUPLICATE CHECK WITH REJECTED
// ============================================

test('bisa membuat opname ulang jika opname sebelumnya ditolak', function () {
    $superAdmin = createSuperAdmin();

    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();

    // Opname sebelumnya ditolak
    Opname::factory()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'opname_date' => today()->format('Y-m-d'),
        'status' => 'rejected',
    ]);

    $data = [
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'physical_qty' => 10,
        'opname_date' => today()->format('Y-m-d'),
    ];

    $response = actingAs($superAdmin)->post(route('opname.store'), $data);

    $response->assertRedirect(route('opname.index'))
        ->assertSessionHas('success', 'Opname berhasil dibuat.');

    // total 2 opnames (1 rejected + 1 new)
    $this->assertDatabaseCount('opnames', 2);
});
