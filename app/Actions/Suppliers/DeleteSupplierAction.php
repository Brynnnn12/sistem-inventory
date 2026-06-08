<?php

namespace App\Actions\Suppliers;

use App\Models\InboundTransaction;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class DeleteSupplierAction
{
    public function execute(Supplier $supplier): void
    {
        DB::transaction(function () use ($supplier) {
            $supplier = Supplier::where('id', $supplier->id)->lockForUpdate()->firstOrFail();

            if (InboundTransaction::where('supplier_id', $supplier->id)->exists()) {
                throw new \Exception("Supplier \"{$supplier->name}\" tidak dapat dihapus karena masih memiliki transaksi barang masuk.");
            }

            $supplier->delete();
        });
    }
}
