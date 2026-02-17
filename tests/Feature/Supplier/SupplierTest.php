<?php

use App\Models\Supplier;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->user = createSuperAdmin();
    actingAs($this->user);
});

test('super-admin bisa melihat daftar supplier', function () {
    $suppliers = Supplier::factory()->count(3)->create(['is_active' => true]);

    $response = actingAs(createSuperAdmin())->get(route('suppliers.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('suppliers/index')
            ->has('suppliers.data', 3)
            ->has('suppliers.data.0', fn ($supplier) => $supplier
                ->has('id')
                ->has('code')
                ->has('name')
                ->has('contact_person')
                ->has('phone')
                ->has('email')
                ->has('address')
                ->has('tax_id')
                ->has('is_active')
                ->has('created_at')
                ->has('updated_at')
            )
        );
});

test('admin bisa melihat daftar supplier', function () {
    Supplier::factory()->count(2)->create(['is_active' => true]);

    $response = actingAs(createAdmin())->get(route('suppliers.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('suppliers/index')
            ->has('suppliers.data', 2)
        );
});

test('viewer bisa melihat daftar supplier', function () {
    Supplier::factory()->count(2)->create(['is_active' => true]);

    $response = actingAs(createViewer())->get(route('suppliers.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('suppliers/index')
        );
});

test('super-admin bisa membuat supplier', function () {
    $data = [
        'name' => 'PT. Contoh Supplier',
        'contact_person' => 'Budi',
        'phone' => '+6281234567890',
        'address' => 'Jl. Contoh No.1',
        'tax_id' => 'NPWP-1234567890',
        'is_active' => true,
    ];

    $response = actingAs(createSuperAdmin())->post(route('suppliers.store'), $data);

    $response->assertRedirect(route('suppliers.index'))
        ->assertSessionHas('success', 'Supplier berhasil dibuat.');

    $this->assertDatabaseHas('suppliers', [
        'name' => 'PT. Contoh Supplier',
        'tax_id' => 'NPWP-1234567890',
    ]);
});

test('admin tidak bisa membuat supplier', function () {
    $data = [
        'name' => 'PT. Admin Supplier',
        'contact_person' => 'Admin CP',
        'phone' => '+628111111111',
        'address' => 'Jl. Admin No.1',
        'tax_id' => 'NPWP-ADMIN-001',
        'is_active' => true,
    ];

    $response = actingAs(createAdmin())->post(route('suppliers.store'), $data);

    $response->assertForbidden();
});

test('viewer tidak bisa membuat supplier', function () {
    $data = [
        'name' => 'PT. Viewer Supplier',
        'contact_person' => 'Viewer CP',
        'phone' => '+628222222222',
        'address' => 'Jl. Viewer No.1',
        'tax_id' => 'NPWP-VIEWER-001',
        'is_active' => true,
    ];

    $response = actingAs(createViewer())->post(route('suppliers.store'), $data);

    $response->assertForbidden();
});

test('super-admin bisa memperbarui supplier', function () {
    $supplier = Supplier::factory()->create();

    $update = [
        'name' => 'PT. Supplier Updated',
        'contact_person' => 'Siti',
        'phone' => '+62899887766',
        'address' => 'Jl. Updated No.2',
        'tax_id' => 'NPWP-9999999999',
        'is_active' => false,
    ];

    $response = actingAs(createSuperAdmin())->put(route('suppliers.update', $supplier), $update);

    $response->assertRedirect(route('suppliers.index'))
        ->assertSessionHas('success', 'Supplier berhasil diperbarui.');

    $supplier->refresh();
    expect($supplier->name)->toBe('PT. Supplier Updated');
    expect($supplier->is_active)->toBeFalse();
});

test('admin tidak bisa memperbarui supplier', function () {
    $supplier = Supplier::factory()->create();

    $payload = [
        'name' => 'Admin Update',
        'contact_person' => 'Admin CP',
        'address' => 'Jl. Admin Update',
    ];

    $response = actingAs(createAdmin())->put(route('suppliers.update', $supplier), $payload);

    $response->assertForbidden();
});

test('viewer tidak bisa memperbarui supplier', function () {
    $supplier = Supplier::factory()->create();

    $payload = [
        'name' => 'Viewer Update',
        'contact_person' => 'Viewer CP',
        'address' => 'Jl. Viewer Update',
    ];

    $response = actingAs(createViewer())->put(route('suppliers.update', $supplier), $payload);

    $response->assertForbidden();
});

test('super-admin bisa menghapus supplier', function () {
    $supplier = Supplier::factory()->create();

    $response = actingAs(createSuperAdmin())->delete(route('suppliers.destroy', $supplier));

    $response->assertRedirect(route('suppliers.index'))
        ->assertSessionHas('success', 'Supplier berhasil dihapus.');

    $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
});

test('admin tidak bisa menghapus supplier', function () {
    $supplier = Supplier::factory()->create();

    $response = actingAs(createAdmin())->delete(route('suppliers.destroy', $supplier));

    $response->assertForbidden();
});

test('viewer tidak bisa menghapus supplier', function () {
    $supplier = Supplier::factory()->create();

    $response = actingAs(createViewer())->delete(route('suppliers.destroy', $supplier));

    $response->assertForbidden();
});

test('super-admin bisa hapus banyak supplier', function () {
    $suppliers = Supplier::factory()->count(3)->create();
    $ids = $suppliers->pluck('id')->toArray();

    $response = actingAs(createSuperAdmin())->delete(route('suppliers.bulk-destroy'), ['ids' => $ids]);

    $response->assertRedirect(route('suppliers.index'))
        ->assertSessionHas('success', 'Berhasil menghapus 3 supplier.');

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('suppliers', ['id' => $id]);
    }
});

