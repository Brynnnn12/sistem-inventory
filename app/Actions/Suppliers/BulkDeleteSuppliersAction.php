<?php

namespace App\Actions\Suppliers;

use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class BulkDeleteSuppliersAction
{
    /**
     * Bulk delete suppliers (soft delete).
     *
     * @param  array<int>  $ids
     *
     * @throws \Exception
     */
    public function execute(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            // Lock for update to prevent race conditions
            $suppliers = Supplier::whereIn('id', $ids)->lockForUpdate()->get();

            // Soft delete - preserves audit trail
            return Supplier::whereIn('id', $ids)->delete();
        });
    }
}
