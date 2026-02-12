<?php

namespace App\Http\Controllers;

use App\Actions\Suppliers\BulkDeleteSuppliersAction;
use App\Actions\Suppliers\CreateSupplierAction;
use App\Actions\Suppliers\DeleteSupplierAction;
use App\Actions\Suppliers\UpdateSupplierAction;
use App\Http\Requests\Suppliers\StoreSupplierRequest;
use App\Http\Requests\Suppliers\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Supplier::class);

        $suppliers = Supplier::query()
            ->search($request->search)
            ->when($request->boolean('active_only'), function ($query) {
                $query->where('is_active', true);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('suppliers/index', [
            'suppliers' => $suppliers,
            'filters' => $request->only(['search', 'active_only']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request, CreateSupplierAction $action)
    {
        $this->authorize('create', Supplier::class);

        $action->execute($request->validated());

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $this->authorize('view', $supplier);

        return Inertia::render('suppliers/show', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier, UpdateSupplierAction $action)
    {
        $this->authorize('update', $supplier);

        $action->execute($supplier, $request->validated());

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier, DeleteSupplierAction $action)
    {
        $this->authorize('delete', $supplier);

        try {
            $action->execute($supplier);

            return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('suppliers.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete suppliers.
     */
    public function bulkDestroy(Request $request, BulkDeleteSuppliersAction $action)
    {
        $this->authorize('bulkDelete', Supplier::class);

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:suppliers,id',
        ]);

        try {
            $count = $action->execute($request->ids);

            return redirect()->route('suppliers.index')->with('success', "Berhasil menghapus {$count} supplier.");
        } catch (\Exception $e) {
            return redirect()->route('suppliers.index')->with('error', $e->getMessage());
        }
    }
}
