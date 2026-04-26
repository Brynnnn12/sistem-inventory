<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Display stock reports page.
     */
    public function stock(Request $request): Response
    {
        $this->authorize('view any reports');

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $warehouseIds = $isSuperAdmin ? null : $user->warehouses->pluck('id')->toArray();

        $warehouseId = $request->get('warehouse_id');
        if ($warehouseId === 'all' || $warehouseId === '' || $warehouseId == 0) {
            $warehouseId = null;
        }

        if ($warehouseId !== null) {
            $warehouseId = (int) $warehouseId;
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $stockReport = $this->reportService->getStockReport($warehouseId, $startDate, $endDate, $warehouseIds);
        $warehouses = $isSuperAdmin
            ? \App\Models\Warehouse::active()->select('id', 'name')->get()
            : $user->warehouses()->active()->select('warehouses.id', 'warehouses.name')->get();

        return Inertia::render('reports/stock', [
            'stockReport' => $stockReport,
            'warehouses' => $warehouses,
            'filters' => [
                'warehouse_id' => $warehouseId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Display transaction reports page.
     */
    public function transactions(Request $request): Response
    {
        $this->authorize('view any reports');

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $warehouseIds = $isSuperAdmin ? null : $user->warehouses->pluck('id')->toArray();

        $type = $request->get('type', 'all'); // inbound, outbound, mutation, all
        $warehouseId = $request->get('warehouse_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $transactionReport = $this->reportService->getTransactionReport($type, $warehouseId, $startDate, $endDate, $warehouseIds);
        $warehouses = $isSuperAdmin
            ? \App\Models\Warehouse::active()->select('id', 'name')->get()
            : $user->warehouses()->active()->select('warehouses.id', 'warehouses.name')->get();

        return Inertia::render('reports/transactions', [
            'transactionReport' => $transactionReport,
            'warehouses' => $warehouses,
            'filters' => [
                'type' => $type,
                'warehouse_id' => $warehouseId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Get stock alerts (low stock, out of stock).
     */
    public function alerts(Request $request): Response
    {
        $this->authorize('view any reports');

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $warehouseIds = $isSuperAdmin ? null : $user->warehouses->pluck('id')->toArray();

        $alerts = $this->reportService->getStockAlerts($warehouseIds);

        return Inertia::render('reports/alerts', [
            'alerts' => $alerts,
        ]);
    }

    /**
     * Export stock report to PDF/Excel.
     */
    public function exportStock(Request $request)
    {
        $this->authorize('view any reports');

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $warehouseIds = $isSuperAdmin ? null : $user->warehouses->pluck('id')->toArray();

        $warehouseId = $request->get('warehouse_id');
        if ($warehouseId === 'all' || $warehouseId === '' || $warehouseId == 0) {
            $warehouseId = null;
        }

        if ($warehouseId !== null) {
            $warehouseId = (int) $warehouseId;
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $format = $request->get('format', 'pdf');

        return $this->reportService->exportStockReport($warehouseId, $startDate, $endDate, $format, $warehouseIds);
    }

    /**
     * Export transaction report to PDF/Excel.
     */
    public function exportTransactions(Request $request)
    {
        $this->authorize('view any reports');

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $warehouseIds = $isSuperAdmin ? null : $user->warehouses->pluck('id')->toArray();

        $type = $request->get('type', 'all');
        $warehouseId = $request->get('warehouse_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $format = $request->get('format', 'pdf');

        return $this->reportService->exportTransactionReport($type, $warehouseId, $startDate, $endDate, $format, $warehouseIds);
    }
}
