<?php

namespace App\Actions\Suppliers;

use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class DeleteSupplierAction
{
    /**
     * Delete a supplier (soft delete).
     *
     * @throws \Exception
     */
    public function execute(Supplier $supplier): void
    {
        DB::transaction(function () use ($supplier) {
            // Lock for update to prevent race conditions
            $supplier = Supplier::where('id', $supplier->id)->lockForUpdate()->firstOrFail();

            // Soft delete - preserves audit trail
            $supplier->delete();
        });
    }
}
