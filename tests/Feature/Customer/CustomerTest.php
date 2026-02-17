<?php

use App\Models\Customer;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;



// ============================================
// INDEX - Tampilkan daftar customer
// ============================================

test('super-admin bisa melihat daftar customer', function () {
    $customers = Customer::factory()->count(3)->create(['is_active' => true]);

    $response = actingAs(createSuperAdmin())->get(route('customers.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('customers/index')
            ->has('customers.data', 3)
            ->has('customers.data.0', fn ($customer) => $customer
                ->has('id')
                ->has('code')
                ->has('name')
                ->has('contact_person')
                ->has('phone')
                ->has('email')
                ->has('address')
                ->has('is_active')
                ->has('created_at')
                ->has('updated_at')
            )
        );
});

test('admin bisa melihat daftar customer', function () {
    Customer::factory()->count(2)->create(['is_active' => true]);

    $response = actingAs(createAdmin())->get(route('customers.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('customers/index')
            ->has('customers.data', 2)
        );
});

test('viewer bisa melihat daftar customer', function () {
    Customer::factory()->count(2)->create(['is_active' => true]);

    $response = actingAs(createViewer())->get(route('customers.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('customers/index')
        );
});

test('user yang tidak terautentikasi tidak bisa melihat daftar customer', function () {
    get(route('customers.index'))
        ->assertRedirect(route('login'));
});

test('user tanpa peran tidak bisa melihat daftar customer', function () {
    $user = User::factory()->create();

    actingAs($user)->get(route('customers.index'))
        ->assertForbidden();
});

// ============================================
// SEARCH & PAGINATION
// ============================================

test('customer bisa dicari berdasarkan nama', function () {
    Customer::factory()->create(['name' => 'Toko Sari Maju', 'is_active' => true]);
    Customer::factory()->create(['name' => 'Restoran Sari', 'is_active' => true]);
    Customer::factory()->create(['name' => 'Kafe Kopi']);

    $response = actingAs(createSuperAdmin())->get(route('customers.index', ['search' => 'Sari']));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('customers/index')
            ->has('customers.data', 2)
            ->where('filters.search', 'Sari')
        );
});

test('customer dipaginasi', function () {
    Customer::factory()->count(15)->create();

    $response = actingAs(createSuperAdmin())->get(route('customers.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('customers.data', 10)
            ->has('customers.links')
            ->where('customers.per_page', 10)
        );
});

// ============================================
// STORE - Buat customer baru (skip `show` tests per permintaan)
// ============================================

test('super-admin bisa membuat customer', function () {
    $data = [
        'name' => 'Toko Baru Sukses',
        'contact_person' => 'Agus',
        'phone' => '+6281234567890',
        'address' => 'Jl. Contoh No.1',
        'is_active' => true,
    ];

    $response = actingAs(createSuperAdmin())->post(route('customers.store'), $data);

    $response->assertRedirect(route('customers.index'))
        ->assertSessionHas('success', 'Customer berhasil dibuat.');

    $this->assertDatabaseHas('customers', [
        'name' => 'Toko Baru Sukses',
        'is_active' => true,
    ]);
});

test('admin tidak bisa membuat customer', function () {
    $data = [
        'name' => 'Toko Admin',
        'contact_person' => 'Admin CP',
    ];

    actingAs(createAdmin())->post(route('customers.store'), $data)
        ->assertForbidden();
});

test('viewer tidak bisa membuat customer', function () {
    $data = [
        'name' => 'Toko Viewer',
        'contact_person' => 'Viewer CP',
    ];

    actingAs(createViewer())->post(route('customers.store'), $data)
        ->assertForbidden();
});

test('nama customer wajib diisi saat membuat', function () {
    actingAs(createSuperAdmin())->post(route('customers.store'), [
        'name' => '',
    ])->assertSessionHasErrors(['name']);
});

// ============================================
// UPDATE - Perbarui customer
// ============================================

test('super-admin bisa memperbarui customer', function () {
    $customer = Customer::factory()->create(['name' => 'Nama Lama']);

    $payload = [
        'name' => 'Nama Baru',
        'contact_person' => 'Siti',
    ];

    $response = actingAs(createSuperAdmin())->put(route('customers.update', $customer), $payload);

    $response->assertRedirect(route('customers.index'))
        ->assertSessionHas('success', 'Customer berhasil diperbarui.');

    $customer->refresh();
    expect($customer->name)->toBe('Nama Baru');
});

test('admin tidak bisa memperbarui customer', function () {
    $customer = Customer::factory()->create();

    actingAs(createAdmin())->put(route('customers.update', $customer), ['name' => 'Admin Update'])
        ->assertForbidden();
});

test('viewer tidak bisa memperbarui customer', function () {
    $customer = Customer::factory()->create();

    actingAs(createViewer())->put(route('customers.update', $customer), ['name' => 'Viewer Update'])
        ->assertForbidden();
});

// ============================================
// DESTROY - Hapus customer
// ============================================

test('super-admin bisa menghapus customer', function () {
    $customer = Customer::factory()->create();

    $response = actingAs(createSuperAdmin())->delete(route('customers.destroy', $customer));

    $response->assertRedirect(route('customers.index'))
        ->assertSessionHas('success', 'Customer berhasil dihapus.');

    $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
});

test('admin tidak bisa menghapus customer', function () {
    $customer = Customer::factory()->create();

    actingAs(createAdmin())->delete(route('customers.destroy', $customer))
        ->assertForbidden();
});

test('viewer tidak bisa menghapus customer', function () {
    $customer = Customer::factory()->create();

    actingAs(createViewer())->delete(route('customers.destroy', $customer))
        ->assertForbidden();
});

// ============================================
// BULK DESTROY
// ============================================

test('super-admin bisa hapus banyak customer', function () {
    $customers = Customer::factory()->count(3)->create();
    $ids = $customers->pluck('id')->toArray();

    $response = actingAs(createSuperAdmin())->delete(route('customers.bulk-destroy'), ['ids' => $ids]);

    $response->assertRedirect(route('customers.index'))
        ->assertSessionHas('success', 'Berhasil menghapus 3 customer.');

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('customers', ['id' => $id]);
    }
});

test('bulk delete requires valid ids', function () {
    actingAs(createSuperAdmin())->delete(route('customers.bulk-destroy'), ['ids' => []])
        ->assertSessionHasErrors(['ids']);

    actingAs(createSuperAdmin())->delete(route('customers.bulk-destroy'), ['ids' => [9999, 8888]])
        ->assertSessionHasErrors(['ids.0']);
});
