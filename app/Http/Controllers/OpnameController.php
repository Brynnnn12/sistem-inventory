<?php

namespace App\Http\Controllers;

use App\Actions\Opname\ApproveOpnameAction;
use App\Actions\Opname\CreateOpnameAction;
use App\Http\Requests\Opname\StoreOpnameRequest;
use App\Models\Opname;
use App\Models\Product;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OpnameController extends Controller
{
    public function __construct(
        private readonly ApproveOpnameAction $approveOpnameAction,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Opname::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Opname::with(['warehouse', 'product', 'creator'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                    ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$request->search}%"));
            })
            ->when($request->warehouse_id, function ($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            })
            ->when($request->difference_type, function ($q) use ($request) {
                $q->where('difference_type', $request->difference_type);
            })
            ->when(! $user->hasRole('super-admin'), function ($q) use ($user) {
                $warehouseIds = $user->warehouses()->pluck('warehouses.id');
                $q->whereIn('warehouse_id', $warehouseIds);
            })
            ->latest();

        $opnames = $query->paginate(15)->withQueryString();

        $warehouses = $user->hasRole('super-admin')
            ? Warehouse::active()->get()
            : $user->warehouses()->active()->get();

        $products = Product::active()->get();

        // Get stocks for product filtering based on accessible warehouses
        $stocks = \App\Models\Stock::with(['product', 'warehouse'])
            ->whereHas('warehouse', function ($q) use ($warehouses) {
                $q->whereIn('id', $warehouses->pluck('id'));
            })
            ->where('quantity', '>=', 0) // Include products with 0 stock for opname
            ->get();

        return Inertia::render('opname/index', [
            'opnames' => $opnames,
            'warehouses' => $warehouses,
            'products' => $products,
            'stocks' => $stocks,
            'filters' => $request->only(['search', 'warehouse_id', 'difference_type', 'start_date', 'end_date']),
        ]);
    }

    public function store(StoreOpnameRequest $request, CreateOpnameAction $action): RedirectResponse
    {
        $this->authorize('create', Opname::class);

        try {
            $action->execute($request->validated());

            return redirect()->route('opname.index')
                ->with('success', 'Opname berhasil dibuat.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {

            return redirect()->back()
                ->with('error', "Gagal membuat opname: {$e->getMessage()}")
                ->withInput();
        }
    }

    public function approve(Request $request, Opname $opname): RedirectResponse
    {
        $this->authorize('approve', $opname);

        try {
            $this->approveOpnameAction->execute($opname->getKey());

            return redirect()->route('opname.index')
                ->with('success', 'Opname berhasil diapprove dan stok disesuaikan.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', "Gagal mengapprove opname: {$e->getMessage()}");
        }
    }
}
