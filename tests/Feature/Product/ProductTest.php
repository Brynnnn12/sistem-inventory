<?php
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;



test('super-admin bisa melihat daftar produk', function () {
    $superAdmin = createSuperAdmin();

    $products = \App\Models\Product::factory()->count(3)->create(['is_active' => true]);

    $response = actingAs($superAdmin)->get(route('products.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('products/index')
            ->has('products.data', 3)
            ->has('products.data.0', fn ($product) => $product
                ->has('id')
                ->has('code')
                ->has('name')
                ->has('unit')
                ->has('category_id')
                ->has('category', fn ($cat) => $cat
                    ->has('id')
                    ->has('name')
                )
                ->has('price')
                ->has('cost')
                ->has('min_stock')
                ->has('max_stock')
                ->has('description')
                ->has('is_active')
                ->has('created_at')
                ->has('updated_at')
                ->has('deleted_at')
            )
        );
});

test('admin bisa melihat daftar produk', function () {
    $admin = createAdmin();
    \App\Models\Product::factory()->count(2)->create(['is_active' => true]);

    $response = actingAs($admin)->get(route('products.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('products/index')
            ->has('products.data', 2)
        );
});

test('viewer bisa melihat daftar produk', function () {
    $viewer = createViewer();
    \App\Models\Product::factory()->count(2)->create(['is_active' => true]);

    $response = actingAs($viewer)->get(route('products.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('products/index')
        );
});

test('super-admin bisa buat produk' , function() {
    $superAdmin = createSuperAdmin();

    $productData = [
        'name' => 'Produk A',
        'unit' => 'Pcs',
        'price' => 10000,
        'cost' => 8000,
        'category_id' => \App\Models\Category::factory()->create()->id,
    ];

    $response = actingAs($superAdmin)->post(route('products.store'), $productData);

    $response->assertRedirect(route('products.index'))
        ->assertSessionHas('success', 'Produk berhasil dibuat.');

    // Verifikasi produk tersimpan di database
    expect(\App\Models\Product::where([
        'name' => 'Produk A',
        'unit' => 'Pcs',
        'category_id' => $productData['category_id'],
    ])->exists())->toBeTrue();

    // Verifikasi harga dan cost tersimpan
    $product = \App\Models\Product::where('name', 'Produk A')->first();
    expect((float) $product->price)->toBe(10000.0);
    expect((float) $product->cost)->toBe(8000.0);
});

test('admin tidak bisa buat produk', function () {
    $admin = createAdmin();

    $productData = [
        'name' => 'Produk B',
        'brand' => 'Merek B',
        'unit' => 'Pcs',
        'selling_price' => 15000,
        'cost_price' => 12000,
        'category_id' => \App\Models\Category::factory()->create()->id,
    ];

    $response = actingAs($admin)->post(route('products.store'), $productData);

    $response->assertForbidden();
});

test('viewer tidak bisa buat produk', function () {
    $viewer = createViewer();

    $productData = [
        'name' => 'Produk C',
        'brand' => 'Merek C',
        'unit' => 'Pcs',
        'selling_price' => 20000,
        'cost_price' => 18000,
        'category_id' => \App\Models\Category::factory()->create()->id,
    ];

    $response = actingAs($viewer)->post(route('products.store'), $productData);

    $response->assertForbidden();
});

test('super-admin bisa update produk', function () {
    $superAdmin = createSuperAdmin();
    $product = \App\Models\Product::factory()->create();

    $updateData = [
        'name' => 'Produk Updated',
        'unit' => 'Box',
        'category_id' => $product->category_id,
    ];

    $response = actingAs($superAdmin)->put(route('products.update', $product), $updateData);

    $response->assertRedirect(route('products.index'))
        ->assertSessionHas('success', 'Produk berhasil diperbarui.');

    // Verifikasi produk terupdate di database
    $product->refresh();
    expect($product->name)->toBe('Produk Updated');
    expect($product->unit)->toBe('Box');
});

test('super-admin bisa hapus produk', function () {
    $superAdmin = createSuperAdmin();
    $product = \App\Models\Product::factory()->create();

    $response = actingAs($superAdmin)->delete(route('products.destroy', $product));

    $response->assertRedirect(route('products.index'))
        ->assertSessionHas('success', 'Produk berhasil dihapus.');

    // Verifikasi produk terhapus (soft delete)
    expect(\App\Models\Product::find($product->id))->toBeNull();
    expect(\App\Models\Product::withTrashed()->find($product->id))->not->toBeNull();
});

test('super-admin bisa hapus banyak produk', function () {
    $superAdmin = createSuperAdmin();
    $products = \App\Models\Product::factory()->count(3)->create();
    $ids = $products->pluck('id')->toArray();

    $response = actingAs($superAdmin)->delete(route('products.bulk-destroy'), ['ids' => $ids]);

    $response->assertRedirect(route('products.index'))
        ->assertSessionHas('success', 'Berhasil menghapus 3 produk.');

    foreach ($ids as $id) {
        expect(\App\Models\Product::find($id))->toBeNull();
        expect(\App\Models\Product::withTrashed()->find($id))->not->toBeNull();
    }
});

test('admin tidak bisa update produk', function () {
    $admin = createAdmin();
    $product = \App\Models\Product::factory()->create();

    $updateData = [
        'name' => 'Produk Updated by Admin',
        'brand' => 'Merek Updated',
        'unit' => 'Box',
        'category_id' => $product->category_id,
    ];

    $response = actingAs($admin)->put(route('products.update', $product), $updateData);

    $response->assertForbidden();
});

test('viewer tidak bisa update produk', function () {
    $viewer = createViewer();
    $product = \App\Models\Product::factory()->create();

    $updateData = [
        'name' => 'Produk Updated by Viewer',
        'brand' => 'Merek Updated',
        'unit' => 'Box',
        'category_id' => $product->category_id,
    ];

    $response = actingAs($viewer)->put(route('products.update', $product), $updateData);

    $response->assertForbidden();
});

test('admin tidak bisa hapus produk', function () {
    $admin = createAdmin();
    $product = \App\Models\Product::factory()->create();

    $response = actingAs($admin)->delete(route('products.destroy', $product));

    $response->assertForbidden();
});

test('viewer tidak bisa hapus produk', function () {
    $viewer = createViewer();
    $product = \App\Models\Product::factory()->create();

    $response = actingAs($viewer)->delete(route('products.destroy', $product));

    $response->assertForbidden();
});
