<?php

namespace App\Actions\Suppliers;

use App\Models\InboundTransaction;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class BulkDeleteSuppliersAction
{
    /**
     * @param  array<int>  $ids
     *
     * @throws \Exception
     */
    public function execute(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $suppliers = Supplier::whereIn('id', $ids)->lockForUpdate()->get();

            $blocked = $suppliers->filter(fn ($s) => InboundTransaction::where('supplier_id', $s->id)->exists());

            if ($blocked->isNotEmpty()) {
                $names = $blocked->pluck('name')->join(', ');
                throw new \Exception("Supplier berikut tidak dapat dihapus karena masih memiliki transaksi barang masuk: {$names}");
            }

            return Supplier::whereIn('id', $ids)->delete();
        });
    }
}
