<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\MutationController;
use App\Http\Controllers\OpnameController;
use App\Http\Controllers\OutboundController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProofDocumentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockHistoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnassignedController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('auth/google/redirect', [ProfileController::class, 'google_redirect'])
    ->middleware('guest')
    ->name('google.redirect');
Route::get('auth/google/callback', [ProfileController::class, 'google_callback'])
    ->middleware('guest')
    ->name('google.callback');

Route::get('unassigned', [UnassignedController::class, 'index'])
    ->middleware(['auth'])
    ->name('unassigned');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'has.warehouse'])
    ->name('dashboard');

Route::prefix('dashboard')->middleware(['auth', 'has.warehouse'])->group(function () {

    Route::middleware(['throttle:bulk'])->group(function () {
        Route::delete('employees/bulk-destroy', [EmployeeController::class, 'bulkDestroy'])
            ->name('employees.bulk-destroy');

        Route::delete('categories/bulk-destroy', [CategoryController::class, 'bulkDestroy'])
            ->name('categories.bulk-destroy');

        Route::delete('warehouses/bulk-destroy', [WarehouseController::class, 'bulkDestroy'])
            ->name('warehouses.bulk-destroy');

        Route::delete('warehouse-users/bulk-destroy', [WarehouseUserController::class, 'bulkDestroy'])
            ->name('warehouse-users.bulk-destroy');

        Route::delete('products/bulk-destroy', [ProductController::class, 'bulkDestroy'])
            ->name('products.bulk-destroy');

        Route::delete('suppliers/bulk-destroy', [SupplierController::class, 'bulkDestroy'])
            ->name('suppliers.bulk-destroy');

        Route::delete('customers/bulk-destroy', [CustomerController::class, 'bulkDestroy'])
            ->name('customers.bulk-destroy');

    });

    // CRUD operations - Rate limit: 30 per minute
    Route::middleware(['throttle:crud'])->group(function () {
        Route::resource('employees', EmployeeController::class)
            ->parameters(['employees' => 'employee'])
            ->except(['create', 'edit', 'show']);

        Route::resource('categories', CategoryController::class)
            ->parameters(['categories' => 'category'])
            ->except(['create', 'edit']);

        Route::resource('warehouses', WarehouseController::class)
            ->parameters(['warehouses' => 'warehouse'])
            ->except(['create', 'edit']);

        Route::resource('warehouse-users', WarehouseUserController::class)
            ->parameters(['warehouse-users' => 'warehouseUser'])
            ->except(['create', 'edit', 'update', 'show']);

        Route::post('warehouse-users/swap', [WarehouseUserController::class, 'swap'])
            ->name('warehouse-users.swap');

        Route::resource('products', ProductController::class)
            ->parameters(['products' => 'product'])
            ->except(['create', 'edit']);

        Route::resource('suppliers', SupplierController::class)
            ->parameters(['suppliers' => 'supplier'])
            ->except(['show'])
            ->except(['create', 'edit']);

        Route::resource('customers', CustomerController::class)
            ->parameters(['customers' => 'customer'])
            ->except(['create', 'edit', 'show']);

        // Stock Management
        Route::prefix('stocks')->name('stocks.')->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
        });

        // Inbound Transactions
        Route::prefix('inbound')->name('inbound.')->group(function () {
            Route::get('/', [InboundController::class, 'index'])->name('index');
            Route::post('/', [InboundController::class, 'store'])->name('store');
            Route::put('/{transaction}', [InboundController::class, 'update'])->name('update');
            Route::delete('/{transaction}', [InboundController::class, 'destroy'])->name('destroy');
        });

        // Outbound Transactions
        Route::prefix('outbound')->name('outbound.')->group(function () {
            Route::get('/', [OutboundController::class, 'index'])->name('index');
            Route::post('/', [OutboundController::class, 'store'])->name('store');
            Route::put('/{transaction}', [OutboundController::class, 'update'])->name('update');
            Route::delete('/{transaction}', [OutboundController::class, 'destroy'])->name('destroy');
        });

        // Opname
        Route::prefix('opname')->name('opname.')->group(function () {
            Route::get('/', [OpnameController::class, 'index'])->name('index');
            Route::post('/', [OpnameController::class, 'store'])->name('store');
            Route::post('/{opname}/approve', [OpnameController::class, 'approve'])->name('approve');
            Route::post('/{opname}/reject', [OpnameController::class, 'reject'])->name('reject');
            Route::delete('/{opname}', [OpnameController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('mutations')->name('mutations.')->group(function () {
            Route::get('/', [MutationController::class, 'index'])->name('index');
            Route::post('/', [MutationController::class, 'store'])->name('store');
            Route::put('/{mutation}', [MutationController::class, 'update'])->name('update');
            Route::delete('/{mutation}', [MutationController::class, 'destroy'])->name('destroy');
            Route::post('/{mutation}/receive', [MutationController::class, 'receive'])->name('receive');
            Route::post('/{mutation}/reject', [MutationController::class, 'reject'])->name('reject');
        });

        // Stock History
        Route::prefix('stock-history')->name('stock-history.')->group(function () {
            Route::get('/', [StockHistoryController::class, 'index'])->name('index');
        });

        // Stock Management
        Route::prefix('stocks')->name('stocks.')->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
            Route::get('/transactions', [ReportController::class, 'transactions'])->name('transactions');
            Route::get('/alerts', [ReportController::class, 'alerts'])->name('alerts');
            Route::get('/stock/export', [ReportController::class, 'exportStock'])->name('stock.export');
            Route::get('/transactions/export', [ReportController::class, 'exportTransactions'])->name('transactions.export');
        });

        // Proof Documents
        Route::get('proof-documents/{type}/{id}/download', [ProofDocumentController::class, 'download'])
            ->name('proof-documents.download')
            ->whereIn('type', ['inbound', 'outbound', 'mutation'])
            ->whereNumber('id');

    });

});

require __DIR__.'/settings.php';
