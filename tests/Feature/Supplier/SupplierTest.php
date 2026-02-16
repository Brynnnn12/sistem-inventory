<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

use App\Models\Supplier;

test('menampilkan flash error saat CreateSupplierAction melempar exception', function () {
    $superAdmin = createSuperAdmin();

    // Bind a fake action that throws
    app()->instance(\App\Actions\Suppliers\CreateSupplierAction::class, new class {
        public function execute(array $input)
        {
            throw new \Exception('Gagal simpan supplier');
        }
    });

    $response = actingAs($superAdmin)->post(route('suppliers.store'), [
        'name' => 'Supplier X',
        'contact_person' => 'CP',
        'phone' => '08123456789',
        'email' => 'x@example.test',
        'address' => 'Alamat',
        'tax_id' => 'TAX-1',
    ]);

    $response->assertRedirect(route('suppliers.index'))
        ->assertSessionHas('error', 'Gagal simpan supplier');
});

test('menampilkan flash error saat UpdateSupplierAction melempar exception', function () {
    $superAdmin = createSuperAdmin();

    $supplier = Supplier::factory()->create();

    app()->instance(\App\Actions\Suppliers\UpdateSupplierAction::class, new class {
        public function execute(Supplier $supplier, array $input)
        {
            throw new \Exception('Gagal update supplier');
        }
    });

    $response = actingAs($superAdmin)->put(route('suppliers.update', $supplier), [
        'name' => 'Supplier Y',
        'contact_person' => 'CP Y',
        'phone' => '08129876543',
        'email' => 'y@example.test',
        'address' => 'Alamat Y',
        'tax_id' => 'TAX-2',
    ]);

    $response->assertRedirect(route('suppliers.index'))
        ->assertSessionHas('error', 'Gagal update supplier');
});
