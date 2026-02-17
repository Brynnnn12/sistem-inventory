<?php

use App\Services\ReportService;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\Product;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;


test('super-admin dapat melihat halaman laporan stok dan menerima data dari service', function () {
    $superAdmin = createSuperAdmin();

    $fakeReport = [
        'data' => [],
        'summary' => ['total_items' => 0, 'total_value' => 0],
        'period' => ['start_date' => '2026-02-01', 'end_date' => '2026-02-28'],
    ];

    $mock = Mockery::mock(ReportService::class);
    $mock->shouldReceive('getStockReport')->once()->andReturn($fakeReport);

    app()->instance(ReportService::class, $mock);

    $response = actingAs($superAdmin)->get(route('reports.stock'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('reports/stock')
            ->where('stockReport', $fakeReport)
            ->has('warehouses')
            ->has('filters')
        );
});

test('user tanpa peran tidak bisa mengakses halaman laporan', function () {
    $user = \App\Models\User::factory()->create();

    $response = actingAs($user)->get(route('reports.stock'));

    $response->assertForbidden();
});

test('transactions page memanggil service dan menampilkan hasil', function () {
    $superAdmin = createSuperAdmin();

    $fakeTx = [
        'data' => [],
        'summary' => [],
        'period' => ['start_date' => '2026-02-01', 'end_date' => '2026-02-28'],
    ];

    $mock = Mockery::mock(ReportService::class);
    $mock->shouldReceive('getTransactionReport')->once()->andReturn($fakeTx);

    app()->instance(ReportService::class, $mock);

    $response = actingAs($superAdmin)->get(route('reports.transactions'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('reports/transactions')
            ->where('transactionReport', $fakeTx)
            ->has('warehouses')
            ->has('filters')
        );
});

test('alerts page mengembalikan alerts sesuai warehouse user', function () {
    $admin = createAdmin();
    $warehouse = Warehouse::factory()->create();
    $admin->warehouses()->sync([$warehouse->id]);

    $fakeAlerts = [
        'low_stock' => [],
        'out_of_stock' => [],
        'total_alerts' => 0,
    ];

    $mock = Mockery::mock(ReportService::class);
    $mock->shouldReceive('getStockAlerts')->once()->with([$warehouse->id])->andReturn($fakeAlerts);

    app()->instance(ReportService::class, $mock);

    $response = actingAs($admin)->get(route('reports.alerts'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('reports/alerts')
            ->where('alerts', $fakeAlerts)
        );
});

test('export stock memanggil service dan mengembalikan response', function () {
    $superAdmin = createSuperAdmin();

    $mock = Mockery::mock(ReportService::class);
    $mock->shouldReceive('exportStockReport')->once()->with(null, Mockery::any(), Mockery::any(), 'excel')
        ->andReturn(response()->json(['file' => 'ok']));

    app()->instance(ReportService::class, $mock);

    $response = actingAs($superAdmin)->get(route('reports.stock.export', ['format' => 'excel']));

    $response->assertOk()->assertJson(['file' => 'ok']);
});

test('export transactions memanggil service dan mengembalikan response', function () {
    $superAdmin = createSuperAdmin();

    $mock = Mockery::mock(ReportService::class);
    $mock->shouldReceive('exportTransactionReport')->once()->with('all', null, Mockery::any(), Mockery::any(), 'pdf')
        ->andReturn(response()->json(['file' => 'tx']));

    app()->instance(ReportService::class, $mock);

    $response = actingAs($superAdmin)->get(route('reports.transactions.export', ['format' => 'pdf']));

    $response->assertOk()->assertJson(['file' => 'tx']);
});
