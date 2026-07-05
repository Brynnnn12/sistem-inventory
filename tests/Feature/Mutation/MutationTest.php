<?php

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMutation;
use App\Models\Warehouse;
use App\Models\WarehouseUser;

use function Pest\Laravel\actingAs;

// INDEX

test('super-admin bisa melihat daftar mutation', function () {
    $superAdmin = createSuperAdmin();

    $date = now()->format('Ymd');
    $w1 = Warehouse::factory()->create(['code' => "WHS-{$date}-01", 'name' => "Gudang A {$date}"]);
    $w2 = Warehouse::factory()->create(['code' => "WHS-{$date}-02", 'name' => "Gudang B {$date}"]);
    $w3 = Warehouse::factory()->create(['code' => "WHS-{$date}-03", 'name' => "Gudang C {$date}"]);

    StockMutation::factory()->create(['code' => "MT-{$date}-001", 'from_warehouse' => $w1->id, 'to_warehouse' => $w2->id]);
    StockMutation::factory()->create(['code' => "MT-{$date}-002", 'from_warehouse' => $w2->id, 'to_warehouse' => $w3->id]);
    StockMutation::factory()->create(['code' => "MT-{$date}-003", 'from_warehouse' => $w3->id, 'to_warehouse' => $w1->id]);

    $response = actingAs($superAdmin)->get(route('mutations.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('mutation/index')
            ->has('mutations.data', 3)
            ->has('warehouses')
            ->has('products')
            ->has('stocks')
        );
});

test('admin hanya melihat mutation terkait gudangnya', function () {
    $admin = createAdmin();

    $date = now()->format('Ymd');
    $warehouseA = Warehouse::factory()->create(['code' => "WHS-{$date}-A", 'name' => "Gudang A {$date}"]);
    $warehouseB = Warehouse::factory()->create(['code' => "WHS-{$date}-B", 'name' => "Gudang B {$date}"]);

    WarehouseUser::factory()->create(['user_id' => $admin->id, 'warehouse_id' => $warehouseA->id]);

    StockMutation::factory()->create(['code' => "MT-{$date}-101", 'from_warehouse' => $warehouseA->id, 'to_warehouse' => $warehouseB->id]);
    StockMutation::factory()->create(['code' => "MT-{$date}-102", 'from_warehouse' => $warehouseB->id, 'to_warehouse' => $warehouseA->id]);

    $response = actingAs($admin)->get(route('mutations.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('mutation/index')
            ->has('mutations.data', 2)
        );
});

// STORE (send)

test('super-admin bisa mengirim mutation', function () {
    $superAdmin = createSuperAdmin();

    $from = Warehouse::factory()->create();
    $to = Warehouse::factory()->create();
    $product = Product::factory()->create();

    Stock::factory()->create([
        'warehouse_id' => $from->id,
        'product_id' => $product->id,
        'quantity' => 20,
        'available_qty' => 20,
    ]);

    $payload = [
        'from_warehouse' => $from->id,
        'to_warehouse' => $to->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ];

    $response = actingAs($superAdmin)->post(route('mutations.store'), $payload);

    $response->assertRedirect(route('mutations.index'))
        ->assertSessionHas('success', 'Mutasi berhasil dikirim.');

    $this->assertDatabaseHas('stock_mutations', [
        'from_warehouse' => $from->id,
        'to_warehouse' => $to->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    // stok gudang asal langsung dikurangi saat send
    $this->assertDatabaseHas('stocks', [
        'warehouse_id' => $from->id,
        'product_id' => $product->id,
        'quantity' => 15,
        'available_qty' => 15,
    ]);
});

test('gagal mengirim mutation jika stok gudang asal tidak cukup', function () {
    $superAdmin = createSuperAdmin();

    $from = Warehouse::factory()->create();
    $to = Warehouse::factory()->create();
    $product = Product::factory()->create();

    // available_qty default 0, kurang dari request 5
    Stock::factory()->create(['warehouse_id' => $from->id, 'product_id' => $product->id, 'quantity' => 2]);

    $payload = [
        'from_warehouse' => $from->id,
        'to_warehouse' => $to->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ];

    $response = actingAs($superAdmin)->post(route('mutations.store'), $payload);

    $response->assertSessionHasErrors(['quantity']);
});

test('validasi store mutation: to_warehouse harus berbeda dari from_warehouse', function () {
    $superAdmin = createSuperAdmin();

    $wh = Warehouse::factory()->create();
    $product = Product::factory()->create();

    $payload = [
        'from_warehouse' => $wh->id,
        'to_warehouse' => $wh->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ];

    $response = actingAs($superAdmin)->post(route('mutations.store'), $payload);

    $response->assertSessionHasErrors(['to_warehouse']);
});

// RECEIVE

test('super-admin bisa menerima mutation dan stok diperbarui', function () {
    $superAdmin = createSuperAdmin();

    $from = Warehouse::factory()->create();
    $to = Warehouse::factory()->create();
    $product = Product::factory()->create();

    $date = now()->format('Ymd');
    $mutation = StockMutation::factory()->pending()->create([
        'code' => "MT-{$date}-201",
        'from_warehouse' => $from->id,
        'to_warehouse' => $to->id,
        'product_id' => $product->id,
        'quantity' => 10,
    ]);

    // Stok gudang asal sudah 0 (sudah dikurangi saat send)
    Stock::factory()->create([
        'warehouse_id' => $from->id,
        'product_id' => $product->id,
        'quantity' => 0,
        'available_qty' => 0,
    ]);

    $response = actingAs($superAdmin)->post(route('mutations.receive', $mutation), [
        'received_qty' => 8,
        'damaged_qty' => 2,
    ]);

    $response->assertRedirect(route('mutations.index'))
        ->assertSessionHas('success', 'Mutasi berhasil diterima.');

    $mutation->refresh();
    expect($mutation->status_display)->toBe('completed');
    expect((float) $mutation->received_qty)->toBe(8.0);
    expect((float) $mutation->damaged_qty)->toBe(2.0);

    // stok gudang asal tetap 0 (tidak dikurangi lagi saat receive)
    $this->assertDatabaseHas('stocks', [
        'warehouse_id' => $from->id,
        'product_id' => $product->id,
        'quantity' => 0,
    ]);

    // stok gudang tujuan bertambah sebesar received_qty (8)
    $this->assertDatabaseHas('stocks', [
        'warehouse_id' => $to->id,
        'product_id' => $product->id,
        'quantity' => 8,
    ]);
});

test('admin yang bertanggung jawab di gudang tujuan bisa menerima mutation', function () {
    $admin = createAdmin();

    $from = Warehouse::factory()->create();
    $to = Warehouse::factory()->create();
    WarehouseUser::factory()->create(['user_id' => $admin->id, 'warehouse_id' => $to->id]);

    $product = Product::factory()->create();

    $date = now()->format('Ymd');
    $mutation = StockMutation::factory()->pending()->create([
        'code' => "MT-{$date}-302",
        'from_warehouse' => $from->id,
        'to_warehouse' => $to->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    // Stok gudang asal sudah 0 (sudah dikurangi saat send)
    Stock::factory()->create([
        'warehouse_id' => $from->id,
        'product_id' => $product->id,
        'quantity' => 0,
        'available_qty' => 0,
    ]);

    $response = actingAs($admin)->post(route('mutations.receive', $mutation), [
        'received_qty' => 5,
        'damaged_qty' => 0,
    ]);

    $response->assertRedirect(route('mutations.index'))
        ->assertSessionHas('success', 'Mutasi berhasil diterima.');

    $this->assertDatabaseHas('stocks', [
        'warehouse_id' => $to->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);
});

test('viewer tidak bisa menerima mutation', function () {
    $viewer = createViewer();

    $from = Warehouse::factory()->create();
    $to = Warehouse::factory()->create();
    $product = Product::factory()->create();

    $date = now()->format('Ymd');
    $mutation = StockMutation::factory()->pending()->create([
        'code' => "MT-{$date}-304",
        'from_warehouse' => $from->id,
        'to_warehouse' => $to->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    actingAs($viewer)->post(route('mutations.receive', $mutation), [
        'received_qty' => 3,
        'damaged_qty' => 0,
    ])->assertForbidden();
});

test('gagal menerima jika jumlah diterima melebihi quantity', function () {
    $superAdmin = createSuperAdmin();

    $from = Warehouse::factory()->create();
    $to = Warehouse::factory()->create();
    $product = Product::factory()->create();

    $date = now()->format('Ymd');
    $mutation = StockMutation::factory()->pending()->create([
        'code' => "MT-{$date}-303",
        'from_warehouse' => $from->id,
        'to_warehouse' => $to->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    $response = actingAs($superAdmin)->post(route('mutations.receive', $mutation), [
        'received_qty' => 6,
        'damaged_qty' => 0,
    ]);

    $response->assertSessionHasErrors(['received_qty']);
});

// REJECT

test('super-admin bisa menolak mutation dan stok dikembalikan', function () {
    $superAdmin = createSuperAdmin();

    $from = Warehouse::factory()->create();
    $product = Product::factory()->create();

    $date = now()->format('Ymd');
    $mutation = StockMutation::factory()->pending()->create([
        'code' => "MT-{$date}-401",
        'from_warehouse' => $from->id,
        'product_id' => $product->id,
        'quantity' => 10,
    ]);

    // Stok sudah dikurangi saat send (dari 10 ke 0)
    Stock::factory()->create([
        'warehouse_id' => $from->id,
        'product_id' => $product->id,
        'quantity' => 0,
        'available_qty' => 0,
    ]);

    $response = actingAs($superAdmin)->post(route('mutations.reject', $mutation), ['notes' => 'Tidak sesuai']);

    $response->assertRedirect(route('mutations.index'))
        ->assertSessionHas('success', 'Mutasi berhasil ditolak.');

    $this->assertDatabaseHas('stock_mutations', ['id' => $mutation->id]);

    // stok dikembalikan ke gudang asal (0 + 10 = 10)
    $this->assertDatabaseHas('stocks', [
        'warehouse_id' => $from->id,
        'product_id' => $product->id,
        'quantity' => 10,
    ]);
});

test('admin di gudang tujuan bisa menolak mutation dan stok dikembalikan', function () {
    $admin = createAdmin();
    $from = Warehouse::factory()->create();
    $to = Warehouse::factory()->create();
    $product = Product::factory()->create();
    WarehouseUser::factory()->create(['user_id' => $admin->id, 'warehouse_id' => $to->id]);

    $date = now()->format('Ymd');
    $mutation = StockMutation::factory()->pending()->create([
        'code' => "MT-{$date}-402",
        'from_warehouse' => $from->id,
        'to_warehouse' => $to->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    // Stok sudah dikurangi saat send (dari 5 ke 0)
    Stock::factory()->create([
        'warehouse_id' => $from->id,
        'product_id' => $product->id,
        'quantity' => 0,
        'available_qty' => 0,
    ]);

    actingAs($admin)->post(route('mutations.reject', $mutation), ['notes' => 'Alasan'])
        ->assertRedirect(route('mutations.index'))
        ->assertSessionHas('success', 'Mutasi berhasil ditolak.');

    // stok dikembalikan ke gudang asal (0 + 5 = 5)
    $this->assertDatabaseHas('stocks', [
        'warehouse_id' => $from->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);
});

test('viewer tidak bisa menolak mutation', function () {
    $viewer = createViewer();
    $date = now()->format('Ymd');
    $mutation = StockMutation::factory()->pending()->create(['code' => "MT-{$date}-403"]);

    actingAs($viewer)->post(route('mutations.reject', $mutation), ['notes' => 'Nope'])->assertForbidden();
});
