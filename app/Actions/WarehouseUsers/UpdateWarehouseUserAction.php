<?php

namespace App\Actions\WarehouseUsers;

use App\Models\WarehouseUser;
use Illuminate\Support\Facades\DB;

class UpdateWarehouseUserAction
{
    public function execute(WarehouseUser $warehouseUser, array $input): WarehouseUser
    {
        return DB::transaction(function () use ($warehouseUser, $input) {
            $oldWarehouseId = $warehouseUser->warehouse_id;
            $oldUserId = $warehouseUser->user_id;
            $newWarehouseId = $input['warehouse_id'];
            $newUserId = $input['user_id'];
            $assignedBy = $input['assigned_by'] ?? auth()->id();
            $assignedAt = $input['assigned_at'] ?? now();
            $isPrimary = $input['is_primary'] ?? $warehouseUser->is_primary;

            if ($oldWarehouseId === $newWarehouseId && $oldUserId === $newUserId) {
                $warehouseUser->update([
                    'assigned_by' => $assignedBy,
                    'assigned_at' => $assignedAt,
                    'is_primary' => $isPrimary,
                ]);

                return $warehouseUser->fresh();
            }

            $warehouseConflict = WarehouseUser::where('warehouse_id', $newWarehouseId)
                ->where('user_id', $newUserId)
                ->where('id', '!=', $warehouseUser->id)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if ($warehouseConflict) {
                // Handle conflict, perhaps delete or update
                $warehouseConflict->delete();
            }

            $warehouseUser->update([
                'user_id' => $newUserId,
                'warehouse_id' => $newWarehouseId,
                'assigned_by' => $assignedBy,
                'assigned_at' => $assignedAt,
                'is_primary' => $isPrimary,
            ]);

            return $warehouseUser->fresh();
        });
    }
}
