<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use App\Models\OutboundTransaction;
use Illuminate\Support\Facades\DB;

class BulkDeleteCustomersAction
{
    /**
     * @param  array<int>  $ids
     *
     * @throws \Exception
     */
    public function execute(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            if (empty($ids)) {
                throw new \Exception('Tidak ada ID pelanggan yang dipilih untuk dihapus');
            }

            $customers = Customer::whereIn('id', $ids)->lockForUpdate()->get();

            $blocked = $customers->filter(fn ($c) => OutboundTransaction::where('customer_id', $c->id)->exists());

            if ($blocked->isNotEmpty()) {
                $names = $blocked->pluck('name')->join(', ');
                throw new \Exception("Customer berikut tidak dapat dihapus karena masih memiliki transaksi barang keluar: {$names}");
            }

            return Customer::whereIn('id', $ids)->delete();
        });
    }
}
