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

            $supplier = Supplier::where('id', $supplier->id)->lockForUpdate()->firstOrFail();

            $supplier->delete();
        });
    }
}
