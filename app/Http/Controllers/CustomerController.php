<?php

namespace App\Http\Controllers;

use App\Actions\Customers\BulkDeleteCustomersAction;
use App\Actions\Customers\CreateCustomerAction;
use App\Actions\Customers\DeleteCustomerAction;
use App\Actions\Customers\UpdateCustomerAction;
use App\Http\Requests\Customers\StoreCustomerRequest;
use App\Http\Requests\Customers\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        $customers = Customer::query()
            ->search($request->search)
            ->when($request->boolean('active_only'), function ($query) {
                $query->where('is_active', true);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('customers/index', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'active_only']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request, CreateCustomerAction $action)
    {
        $this->authorize('create', Customer::class);

        $action->execute($request->validated());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);

        return Inertia::render('customers/show', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer, UpdateCustomerAction $action)
    {
        $this->authorize('update', $customer);

        $action->execute($customer, $request->validated());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer, DeleteCustomerAction $action)
    {
        $this->authorize('delete', $customer);

        try {
            $action->execute($customer);

            return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('customers.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete customers.
     */
    public function bulkDestroy(Request $request, BulkDeleteCustomersAction $action)
    {
        $this->authorize('bulkDelete', Customer::class);

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:customers,id',
        ]);

        try {
            $count = $action->execute($request->ids);

            return redirect()->route('customers.index')->with('success', "Berhasil menghapus {$count} customer.");
        } catch (\Exception $e) {
            return redirect()->route('customers.index')->with('error', $e->getMessage());
        }
    }
}
