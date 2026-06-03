<?php

declare(strict_types=1);

namespace App\Actions\Warehouses;

use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DeleteWarehouseAction
{
    /**
     * Delete a warehouse (soft delete).
     *
     * @throws InvalidArgumentException
     */
    public function execute(Warehouse $warehouse): bool
    {
        return DB::transaction(function () use ($warehouse) {
            $warehouse = Warehouse::where('id', $warehouse->id)->lockForUpdate()->firstOrFail();

            if ($warehouse->users()->exists()) {
                throw new InvalidArgumentException('Tidak dapat menghapus gudang yang masih memiliki pengguna terkait.');
            }

            return $warehouse->delete();
        });
    }
}
