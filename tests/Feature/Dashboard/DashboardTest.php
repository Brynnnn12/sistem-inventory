<?php

use App\Models\InboundTransaction;
use App\Models\OutboundTransaction;
use App\Models\Warehouse;

use function Pest\Laravel\actingAs;

it('redirects admin without warehouse placement to unassigned page', function () {
    $admin = createAdmin(withWarehouse: false);

    actingAs($admin)->get(route('dashboard'))->assertRedirect(route('unassigned'));
});

it('allows admin with warehouse placement to access dashboard', function () {
    $admin = createAdmin();
    $warehouse = Warehouse::factory()->create();
    $admin->warehouses()->attach($warehouse->id);

    actingAs($admin)->get(route('dashboard'))->assertOk();
});

it('allows viewer without warehouse placement to access dashboard', function () {
    $viewer = createViewer(withWarehouse: false);

    actingAs($viewer)->get(route('dashboard'))->assertOk();
});

it('allows super admin without warehouse placement to access dashboard', function () {
    $superAdmin = createSuperAdmin();

    actingAs($superAdmin)->get(route('dashboard'))->assertOk();
});

it('provides a twelve-item monthlyChart on dashboard', function () {
    $superAdmin = createSuperAdmin();

    // make sure at least one inbound/outbound exists so sums are non-zero
    InboundTransaction::factory()->create([
        'received_date' => now()->startOfYear()->toDateString(),
        'quantity' => 5,
    ]);
    OutboundTransaction::factory()->create([
        'sale_date' => now()->startOfYear()->addMonth()->toDateString(),
        'quantity' => 3,
    ]);

    $response = actingAs($superAdmin)->get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->has('monthlyChart', 12)
            ->has('monthlyChart.0', fn ($entry) => $entry
                ->has('month')
                ->has('inbound')
                ->has('outbound')
            )
        );
});
