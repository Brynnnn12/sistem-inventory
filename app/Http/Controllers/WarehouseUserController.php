<?php

namespace App\Http\Controllers;

use App\Actions\WarehouseUsers\BulkDeleteWarehouseUsersAction;
use App\Actions\WarehouseUsers\CreateWarehouseUserAction;
use App\Actions\WarehouseUsers\DeleteWarehouseUserAction;
use App\Actions\WarehouseUsers\SwapWarehouseUsersAction;
use App\Http\Requests\WarehouseUsers\StoreWarehouseUserRequest;
use App\Http\Requests\WarehouseUsers\SwapWarehouseUsersRequest;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseUser;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WarehouseUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', WarehouseUser::class);

        $warehouseUsers = WarehouseUser::query()
            ->with(['warehouse:id,name', 'user:id,name,email', 'assignedBy:id,name'])
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at'); // Only users that are not soft deleted
            })
            ->whereHas('warehouse', function ($query) {
                $query->whereNull('deleted_at'); // Only warehouses that are not soft deleted
            })
            ->search($request->search)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $warehouses = Warehouse::select('id', 'name')->whereNull('deleted_at')->get();

        $assignedUserIds = WarehouseUser::whereNull('deleted_at')->pluck('user_id');
        $users = User::role('admin')
            ->select('id', 'name', 'email')
            ->whereNull('deleted_at')
            ->whereNotIn('id', $assignedUserIds)
            ->get();

        return Inertia::render('warehouse-users/index', [
            'warehouseUsers' => $warehouseUsers,
            'warehouses' => $warehouses,
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWarehouseUserRequest $request, CreateWarehouseUserAction $action)
    {
        $this->authorize('create', WarehouseUser::class);

        try {
            $action->execute($request->validated());

            return redirect()->route('warehouse-users.index')->with('success', 'Penempatan berhasil dibuat.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->route('warehouse-users.index')->with('error', 'Data penempatan sudah ada dan tidak dapat ditambahkan lagi.');
            }

            throw $e;
        } catch (\Exception $e) {
            return redirect()->route('warehouse-users.index')->with('error', 'Gagal membuat penempatan: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WarehouseUser $warehouseUser, DeleteWarehouseUserAction $action)
    {
        $this->authorize('delete', $warehouseUser);

        try {
            $action->execute($warehouseUser);

            return redirect()->route('warehouse-users.index')->with('success', 'Penempatan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('warehouse-users.index')->with('error', 'Gagal menghapus penempatan: '.$e->getMessage());
        }
    }

    /**
     * Bulk delete warehouse users.
     */
    public function bulkDestroy(Request $request, BulkDeleteWarehouseUsersAction $action)
    {
        $this->authorize('bulkDelete', WarehouseUser::class);

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:warehouse_users,id',
        ]);

        try {
            $action->execute($request->input('ids'));

            return redirect()->route('warehouse-users.index')->with('success', 'Penempatan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('warehouse-users.index')->with('error', 'Gagal menghapus penempatan: '.$e->getMessage());
        }
    }

    /**
     * Swap warehouse assignments between two users.
     */
    public function swap(SwapWarehouseUsersRequest $request, SwapWarehouseUsersAction $action)
    {
        $this->authorize('swap', WarehouseUser::class);

        try {
            $action->execute($request->input('warehouse_user1_id'), $request->input('warehouse_user2_id'));

            return redirect()->route('warehouse-users.index')->with('success', 'Penempatan gudang berhasil ditukar.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->route('warehouse-users.index')->with('error', 'Tidak dapat menukar penempatan karena data sudah ada.');
            }

            throw $e;
        } catch (\Exception $e) {
            return redirect()->route('warehouse-users.index')->with('error', 'Gagal menukar penempatan: '.$e->getMessage());
        }
    }
}
