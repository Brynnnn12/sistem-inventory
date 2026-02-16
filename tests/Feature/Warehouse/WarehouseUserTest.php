<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

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

test('admin tidak bisa menukar penempatan gudang (forbidden)', function () {
    $admin = createAdmin();

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

    $response = actingAs($admin)->post(route('warehouse-users.swap'), [
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

    $response->assertRedirect(route('warehouse-users.index'))
        ->assertSessionHas('error', 'Tidak bisa swap dengan diri sendiri');

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
