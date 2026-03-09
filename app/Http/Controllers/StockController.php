<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Stock::with(['warehouse', 'product.category', 'histories' => function ($query) {
            $query->with('user')->latest()->limit(10);
        }]);

        // Filter by user warehouses if not super-admin
        $query->when(! $user->hasRole('super-admin'), function ($q) use ($user) {
            $warehouseIds = $user->warehouses()->pluck('warehouses.id');
            $q->whereIn('warehouse_id', $warehouseIds);
        });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })->orWhereHas('warehouse', function ($warehouseQuery) use ($search) {
                    $warehouseQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        // Filter warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $stocks = $query->paginate(15)->withQueryString();

        return Inertia::render('stocks/index', [
            'stocks' => $stocks,
            'warehouses' => $user->hasRole('super-admin')
                ? Warehouse::active()->select('id', 'name')->get()
                : $user->warehouses()->active()->select('warehouses.id', 'warehouses.name')->get(),
            'products' => Product::active()->select('id', 'name')->get(),
            'filters' => $request->only(['search', 'warehouse_id', 'product_id']),
        ]);
    }
}
