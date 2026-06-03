<?php

declare(strict_types=1);

namespace App\Actions\WarehouseUsers;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreateWarehouseUserAction
{
    public function execute(array $input): WarehouseUser
    {
        return DB::transaction(function () use ($input) {
            $warehouseId = (int) $input['warehouse_id'];
            $userId = (int) $input['user_id'];

            if (! Warehouse::where('id', $warehouseId)->exists()) {
                throw new InvalidArgumentException('Gudang tidak ditemukan');
            }

            if (! User::where('id', $userId)->exists()) {
                throw new InvalidArgumentException('Pengguna tidak ditemukan');
            }

            $assignedBy = $input['assigned_by'] ?? Auth::id();
            $assignedAt = $input['assigned_at'] ?? now();
            $isPrimary = $input['is_primary'] ?? true;

            $existing = WarehouseUser::withTrashed()
                ->where('warehouse_id', $warehouseId)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }

                $existing->update([
                    'assigned_by' => $assignedBy,
                    'assigned_at' => $assignedAt,
                    'is_primary' => $isPrimary,
                ]);

                return $existing->fresh();
            }

            return WarehouseUser::create([
                'user_id' => $userId,
                'warehouse_id' => $warehouseId,
                'assigned_by' => $assignedBy,
                'assigned_at' => $assignedAt,
                'is_primary' => $isPrimary,
            ]);
        });
    }
}
