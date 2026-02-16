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

    // Bind a fake action that throws
    app()->instance(\App\Actions\Opname\CreateOpnameAction::class, new class {
        public function execute(array $input)
        {
            throw new \Exception('Boom');
        }
    });

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
