<?php

use App\Models\InboundTransaction;
use App\Models\OutboundTransaction;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

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
