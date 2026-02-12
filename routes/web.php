<?php

use App\Http\Controllers\Settings\ProfileController;
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


Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::prefix('dashboard')->middleware(['auth', 'verified'])->group(function () {

    Route::middleware(['throttle:bulk'])->group(function () {
        Route::delete('employees/bulk-destroy', [\App\Http\Controllers\EmployeeController::class, 'bulkDestroy'])
            ->name('employees.bulk-destroy');

        Route::delete('categories/bulk-destroy', [\App\Http\Controllers\CategoryController::class, 'bulkDestroy'])
            ->name('categories.bulk-destroy');

        Route::delete('warehouses/bulk-destroy', [\App\Http\Controllers\WarehouseController::class, 'bulkDestroy'])
            ->name('warehouses.bulk-destroy');

        Route::delete('warehouse-users/bulk-destroy', [\App\Http\Controllers\WarehouseUserController::class, 'bulkDestroy'])
            ->name('warehouse-users.bulk-destroy');

        Route::delete('products/bulk-destroy', [\App\Http\Controllers\ProductController::class, 'bulkDestroy'])
            ->name('products.bulk-destroy');

        Route::delete('suppliers/bulk-destroy', [\App\Http\Controllers\SupplierController::class, 'bulkDestroy'])
            ->name('suppliers.bulk-destroy');

        Route::delete('customers/bulk-destroy', [\App\Http\Controllers\CustomerController::class, 'bulkDestroy'])
            ->name('customers.bulk-destroy');

    });


    // CRUD operations - Rate limit: 30 per minute
    Route::middleware(['throttle:crud'])->group(function () {
        Route::resource('employees', \App\Http\Controllers\EmployeeController::class)
            ->parameters(['employees' => 'employee'])
            ->except(['create', 'edit']);

        Route::resource('categories', \App\Http\Controllers\CategoryController::class)
            ->parameters(['categories' => 'category'])
            ->except(['create', 'edit']);

        Route::resource('warehouses', \App\Http\Controllers\WarehouseController::class)
            ->parameters(['warehouses' => 'warehouse'])
            ->except(['create', 'edit']);

        Route::resource('warehouse-users', \App\Http\Controllers\WarehouseUserController::class)
            ->parameters(['warehouse-users' => 'warehouseUser'])
            ->except(['create', 'edit']);

        Route::resource('products', \App\Http\Controllers\ProductController::class)
            ->parameters(['products' => 'product'])
            ->except(['create', 'edit']);

        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)
            ->parameters(['suppliers' => 'supplier'])
            ->except(['create', 'edit']);

        Route::resource('customers', \App\Http\Controllers\CustomerController::class)
            ->parameters(['customers' => 'customer'])
            ->except(['create', 'edit']);

    });

});


require __DIR__.'/settings.php';
