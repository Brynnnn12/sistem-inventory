<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display dashboard with products and employees data.
     */
    public function index(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $warehouseIds = $isSuperAdmin ? null : $user->warehouses()->pluck('warehouses.id')->toArray();

        // Get latest 5 products with category
        $products = \App\Models\Product::query()
            ->active()
            ->with(['category:id,name'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'unit' => $product->unit,
                    'price' => $product->price,
                    'is_active' => $product->is_active,
                    'image_url' => $product->image_url,
                    'category' => $product->category,
                ];
            });

        // Get latest 5 employees
        $employees = \App\Models\User::query()
            ->select('id', 'name', 'email', 'created_at')
            ->where('is_active', true)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->first()?->name,
                    'is_active' => $user->is_active ?? true,
                    'created_at' => $user->created_at,
                ];
            });

        // Get stock summary
        $stockSummary = $this->getStockSummary($warehouseIds);

        // Get recent transactions
        $recentTransactions = $this->getRecentTransactions($warehouseIds);

        // Get stock alerts
        $stockAlerts = $this->getStockAlerts($warehouseIds);

        // Get monthly transaction chart data
        $monthlyChart = $this->getMonthlyTransactionChart($warehouseIds);

        return Inertia::render('dashboard', [
            'products' => $products,
            'employees' => $employees,
            'stockSummary' => $stockSummary,
            'recentTransactions' => $recentTransactions,
            'stockAlerts' => $stockAlerts,
            'monthlyChart' => $monthlyChart,
        ]);
    }

    private function getStockSummary(?array $warehouseIds = null): array
    {
        $totalProducts = \App\Models\Product::active()->count();
        $totalWarehouses = $warehouseIds ? count($warehouseIds) : \App\Models\Warehouse::count();
        $totalStockValueQuery = \App\Models\Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at');
        if ($warehouseIds) {
            $totalStockValueQuery->whereIn('stocks.warehouse_id', $warehouseIds);
        }
        $totalStockValue = $totalStockValueQuery->sum(DB::raw('stocks.quantity * products.cost'));

        $lowStockQuery = \App\Models\Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at')
            ->where('products.min_stock', '>', 0)
            ->whereRaw('stocks.quantity <= products.min_stock')
            ->where('stocks.quantity', '>', 0);
        if ($warehouseIds) {
            $lowStockQuery->whereIn('stocks.warehouse_id', $warehouseIds);
        }
        $lowStockCount = $lowStockQuery->count();

        $outOfStockQuery = \App\Models\Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at')
            ->where('stocks.quantity', '<=', 0);
        if ($warehouseIds) {
            $outOfStockQuery->whereIn('stocks.warehouse_id', $warehouseIds);
        }
        $outOfStockCount = $outOfStockQuery->count();

        return [
            'total_products' => $totalProducts,
            'total_warehouses' => $totalWarehouses,
            'total_stock_value' => $totalStockValue,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
        ];
    }

    private function getRecentTransactions(?array $warehouseIds = null): array
    {
        $inboundQuery = \App\Models\InboundTransaction::with(['product', 'warehouse', 'supplier']);
        if ($warehouseIds) {
            $inboundQuery->whereIn('warehouse_id', $warehouseIds);
        }
        $inbound = $inboundQuery->latest('received_date')
            ->take(3)
            ->get()
            ->map(function ($transaction) {
                return [
                    'type' => 'inbound',
                    'code' => $transaction->code,
                    'date' => $transaction->received_date,
                    'product' => $transaction->product->name,
                    'warehouse' => $transaction->warehouse->name,
                    'quantity' => $transaction->quantity,
                    'supplier' => $transaction->supplier->name,
                ];
            });

        $outboundQuery = \App\Models\OutboundTransaction::with(['product', 'warehouse', 'customer']);
        if ($warehouseIds) {
            $outboundQuery->whereIn('warehouse_id', $warehouseIds);
        }
        $outbound = $outboundQuery->latest('sale_date')
            ->take(3)
            ->get()
            ->map(function ($transaction) {
                return [
                    'type' => 'outbound',
                    'code' => $transaction->code,
                    'date' => $transaction->sale_date,
                    'product' => $transaction->product->name,
                    'warehouse' => $transaction->warehouse->name,
                    'quantity' => $transaction->quantity,
                    'customer' => $transaction->customer->name,
                ];
            });

        $mutationsQuery = \App\Models\StockMutation::with(['product', 'fromWarehouse', 'toWarehouse']);
        if ($warehouseIds) {
            $mutationsQuery->where(function ($q) use ($warehouseIds) {
                $q->whereIn('from_warehouse', $warehouseIds)
                    ->orWhereIn('to_warehouse', $warehouseIds);
            });
        }
        $mutations = $mutationsQuery->latest('sent_at')
            ->take(3)
            ->get()
            ->map(function ($mutation) {
                return [
                    'type' => 'mutation',
                    'code' => $mutation->code,
                    'date' => $mutation->sent_at,
                    'product' => $mutation->product->name,
                    'from_warehouse' => $mutation->fromWarehouse->name,
                    'to_warehouse' => $mutation->toWarehouse->name,
                    'quantity' => $mutation->quantity,
                    'status' => $mutation->status,
                ];
            });

        $transactions = collect([...$inbound, ...$outbound, ...$mutations])
            ->sortByDesc('date')
            ->take(5)
            ->values()
            ->all();

        return $transactions;
    }

    private function getStockAlerts(?array $warehouseIds = null): array
    {
        $lowStockQuery = \App\Models\Stock::with(['product', 'warehouse'])
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at')
            ->where('products.min_stock', '>', 0)
            ->whereRaw('stocks.quantity <= products.min_stock')
            ->where('stocks.quantity', '>', 0);
        if ($warehouseIds) {
            $lowStockQuery->whereIn('stocks.warehouse_id', $warehouseIds);
        }
        $lowStock = $lowStockQuery->select('stocks.*', 'products.name as product_name', 'products.min_stock', 'products.unit')
            ->with(['warehouse'])
            ->take(5)
            ->get()
            ->map(function ($stock) {
                return [
                    'type' => 'low_stock',
                    'message' => "Stok {$stock->product_name} di {$stock->warehouse->name} rendah",
                    'current_qty' => $stock->quantity,
                    'min_stock' => $stock->min_stock,
                    'unit' => $stock->unit,
                ];
            });

        $outOfStockQuery = \App\Models\Stock::with(['product', 'warehouse'])
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at')
            ->where('stocks.quantity', '<=', 0);
        if ($warehouseIds) {
            $outOfStockQuery->whereIn('stocks.warehouse_id', $warehouseIds);
        }
        $outOfStock = $outOfStockQuery->select('stocks.*', 'products.name as product_name', 'products.min_stock', 'products.unit')
            ->with(['warehouse'])
            ->take(5)
            ->get()
            ->map(function ($stock) {
                return [
                    'type' => 'out_of_stock',
                    'message' => "Stok {$stock->product_name} di {$stock->warehouse->name} habis",
                    'current_qty' => $stock->quantity,
                    'min_stock' => $stock->min_stock,
                    'unit' => $stock->unit,
                ];
            });

        return collect([...$lowStock, ...$outOfStock])->take(5)->all();
    }

    private function getMonthlyTransactionChart(?array $warehouseIds = null): array
    {
        $currentYear = now()->year;
        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = \Carbon\Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endDate = \Carbon\Carbon::create($currentYear, $month, 1)->endOfMonth();

            $inboundQuery = \App\Models\InboundTransaction::whereBetween('received_date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
            ]);
            if ($warehouseIds) {
                $inboundQuery->whereIn('warehouse_id', $warehouseIds);
            }
            $inbound = $inboundQuery->sum('quantity');

            $outboundQuery = \App\Models\OutboundTransaction::whereBetween('sale_date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
            ]);
            if ($warehouseIds) {
                $outboundQuery->whereIn('warehouse_id', $warehouseIds);
            }
            $outbound = $outboundQuery->sum('quantity');

            // use Indonesian full month name for clearer labels
            $monthName = $startDate->locale('id')->isoFormat('MMMM');

            $monthlyData[] = [
                'month' => $monthName,
                'inbound' => (int) $inbound,
                'outbound' => (int) $outbound,
            ];
        }

        return $monthlyData;
    }
}
