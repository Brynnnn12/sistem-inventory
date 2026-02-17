<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\delete;

use App\Models\Warehouse;
use App\Models\WarehouseUser;

test('super-admin bisa menukar penempatan gudang antar pengguna', function () {
    $superAdmin = createSuperAdmin();

    $userA = createAdmin();
    $userB = createAdmin();

    $warehouse1 = Warehouse::factory()->create();
    $warehouse2 = Warehouse::factory()->create();

    $wu1 = WarehouseUser::factory()->create([
        'user_id' => $userA->id,
        'warehouse_id' => $warehouse1->id,
    ]);

    $wu2 = WarehouseUser::factory()->create([
        'user_id' => $userB->id,
        'warehouse_id' => $warehouse2->id,
    ]);

    $response = actingAs($superAdmin)->post(route('warehouse-users.swap'), [
        'warehouse_user1_id' => $wu1->id,
        'warehouse_user2_id' => $wu2->id,
    ]);

    $response->assertRedirect(route('warehouse-users.index'))
        ->assertSessionHas('success', 'Penempatan gudang berhasil ditukar.');

    $wu1->refresh();
    $wu2->refresh();

    expect($wu1->warehouse_id)->toBe($warehouse2->id);
    expect($wu2->warehouse_id)->toBe($warehouse1->id);
});

test('super-admin bisa melihat daftar penempatan gudang', function () {
    $superAdmin = createSuperAdmin();

    $users = \App\Models\User::factory()->count(3)->create();
    $warehouses = Warehouse::factory()->count(3)->create();

    $warehouseUsers = collect(range(0,2))->map(fn($i) => WarehouseUser::factory()->create([
        'user_id' => $users[$i]->id,
        'warehouse_id' => $warehouses[$i]->id,
    ]));

    // ensure role `admin` exists for controller's User::role('admin') call
    createAdmin();

    $response = actingAs($superAdmin)->get(route('warehouse-users.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('warehouse-users/index')
            ->has('warehouseUsers.data', 3)
            ->has('warehouseUsers.data.0', fn ($wu) => $wu
                ->has('id')
                ->has('user_id')
                ->has('warehouse_id')
                ->has('user')
                ->has('warehouse')
                ->has('assigned_by')
                ->has('assigned_at')
                ->has('is_primary')
                ->has('created_at')
                ->has('updated_at')
                ->has('deleted_at')
            )
        );
});

test('admin dan viewer tidak bisa melihat daftar penempatan gudang', function () {
    $admin = createAdmin();
    $viewer = createViewer();

    $users = \App\Models\User::factory()->count(2)->create();
    $warehouses = Warehouse::factory()->count(2)->create();

    foreach (range(0,1) as $i) {
        WarehouseUser::factory()->create([
            'user_id' => $users[$i]->id,
            'warehouse_id' => $warehouses[$i]->id,
        ]);
    }

    actingAs($admin)->get(route('warehouse-users.index'))->assertForbidden();
    actingAs($viewer)->get(route('warehouse-users.index'))->assertForbidden();
});


test('super-admin bisa membuat penempatan gudang', function () {
    $superAdmin = createSuperAdmin();

    $adminUser = createAdmin();
    $warehouse = Warehouse::factory()->create();

    $response = actingAs($superAdmin)->post(route('warehouse-users.store'), [
        'user_id' => $adminUser->id,
        'warehouse_id' => $warehouse->id,
        'is_primary' => true,
    ]);

    $response->assertRedirect(route('warehouse-users.index'))
        ->assertSessionHas('success', 'Penempatan berhasil.');

    $this->assertDatabaseHas('warehouse_users', [
        'user_id' => $adminUser->id,
        'warehouse_id' => $warehouse->id,
    ]);
});


test('admin dan viewer tidak bisa membuat penempatan gudang', function () {
    $admin = createAdmin();
    $viewer = createViewer();

    $user = \App\Models\User::factory()->create();
    $warehouse = Warehouse::factory()->create();

    actingAs($admin)->post(route('warehouse-users.store'), [
        'user_id' => $user->id,
        'warehouse_id' => $warehouse->id,
    ])->assertForbidden();

    actingAs($viewer)->post(route('warehouse-users.store'), [
        'user_id' => $user->id,
        'warehouse_id' => $warehouse->id,
    ])->assertForbidden();
});


test('super-admin bisa menghapus penempatan gudang', function () {
    $superAdmin = createSuperAdmin();

    $wu = WarehouseUser::factory()->create();

    $response = actingAs($superAdmin)->delete(route('warehouse-users.destroy', $wu));

    $response->assertRedirect(route('warehouse-users.index'))
        ->assertSessionHas('success', 'Penempatan berhasil dihapus.');

    expect(\App\Models\WarehouseUser::find($wu->id))->toBeNull();
    expect(\App\Models\WarehouseUser::withTrashed()->find($wu->id))->not->toBeNull();
});


test('admin dan viewer tidak bisa menghapus penempatan gudang', function () {
    $admin = createAdmin();
    $viewer = createViewer();

    $wu = WarehouseUser::factory()->create();

    actingAs($admin)->delete(route('warehouse-users.destroy', $wu))->assertForbidden();
    actingAs($viewer)->delete(route('warehouse-users.destroy', $wu))->assertForbidden();
});


test('super-admin bisa hapus banyak penempatan gudang', function () {
    $superAdmin = createSuperAdmin();

    $users = \App\Models\User::factory()->count(3)->create();
    $warehouses = Warehouse::factory()->count(3)->create();

    $wus = collect(range(0,2))->map(fn($i) => WarehouseUser::factory()->create([
        'user_id' => $users[$i]->id,
        'warehouse_id' => $warehouses[$i]->id,
    ]));

    $ids = $wus->pluck('id')->toArray();

    $response = actingAs($superAdmin)->delete(route('warehouse-users.bulk-destroy'), ['ids' => $ids]);

    $response->assertRedirect(route('warehouse-users.index'))
        ->assertSessionHas('success', 'Penempatan berhasil dihapus.');

    foreach ($ids as $id) {
        // soft deleted
        expect(\App\Models\WarehouseUser::find($id))->toBeNull();
        expect(\App\Models\WarehouseUser::withTrashed()->find($id))->not->toBeNull();
    }
});


test('admin dan viewer tidak bisa hapus banyak penempatan gudang', function () {
    $admin = createAdmin();
    $viewer = createViewer();

    $users = \App\Models\User::factory()->count(2)->create();
    $warehouses = Warehouse::factory()->count(2)->create();

    $wus = collect(range(0,1))->map(fn($i) => WarehouseUser::factory()->create([
        'user_id' => $users[$i]->id,
        'warehouse_id' => $warehouses[$i]->id,
    ]));

    $ids = $wus->pluck('id')->toArray();

    actingAs($admin)->delete(route('warehouse-users.bulk-destroy'), ['ids' => $ids])->assertForbidden();
    actingAs($viewer)->delete(route('warehouse-users.bulk-destroy'), ['ids' => $ids])->assertForbidden();
});


test('viewer tidak bisa menukar penempatan gudang (forbidden)', function () {
    $viewer = createViewer();

    $userA = \App\Models\User::factory()->create();
    $userB = \App\Models\User::factory()->create();

    $warehouse1 = Warehouse::factory()->create();
    $warehouse2 = Warehouse::factory()->create();

    $wu1 = WarehouseUser::factory()->create([
        'user_id' => $userA->id,
        'warehouse_id' => $warehouse1->id,
    ]);

    $wu2 = WarehouseUser::factory()->create([
        'user_id' => $userB->id,
        'warehouse_id' => $warehouse2->id,
    ]);

    $response = actingAs($viewer)->post(route('warehouse-users.swap'), [
        'warehouse_user1_id' => $wu1->id,
        'warehouse_user2_id' => $wu2->id,
    ]);

    $response->assertForbidden();
});

test('super-admin gagal swap jika mengirimkan kedua id yang sama', function () {
    $superAdmin = createSuperAdmin();

    $user = createAdmin();
    $warehouse = Warehouse::factory()->create();

    $wu = WarehouseUser::factory()->create([
        'user_id' => $user->id,
        'warehouse_id' => $warehouse->id,
    ]);

    $response = actingAs($superAdmin)->post(route('warehouse-users.swap'), [
        'warehouse_user1_id' => $wu->id,
        'warehouse_user2_id' => $wu->id,
    ]);

    // Validation should prevent same IDs (different:warehouse_user1_id)
    $response->assertStatus(302)
        ->assertSessionHasErrors(['warehouse_user2_id' => 'ID warehouse user harus berbeda.']);

    $wu->refresh();
    expect($wu->warehouse_id)->toBe($warehouse->id);
});

test('super-admin gagal swap jika assignment sama (user atau warehouse sama)', function () {
    $superAdmin = createSuperAdmin();

    $sameUser = createAdmin();
    $warehouse1 = Warehouse::factory()->create();
    $warehouse2 = Warehouse::factory()->create();

    // dua record dengan user yang sama
    $wu1 = WarehouseUser::factory()->create([
        'user_id' => $sameUser->id,
        'warehouse_id' => $warehouse1->id,
    ]);

    $wu2 = WarehouseUser::factory()->create([
        'user_id' => $sameUser->id,
        'warehouse_id' => $warehouse2->id,
    ]);

    $response = actingAs($superAdmin)->post(route('warehouse-users.swap'), [
        'warehouse_user1_id' => $wu1->id,
        'warehouse_user2_id' => $wu2->id,
    ]);

    $response->assertRedirect(route('warehouse-users.index'))
        ->assertSessionHas('error', 'Tidak bisa swap assignment yang sama.');

    $wu1->refresh();
    $wu2->refresh();

    expect($wu1->warehouse_id)->toBe($warehouse1->id);
    expect($wu2->warehouse_id)->toBe($warehouse2->id);
});
