<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use App\Models\OutboundTransaction;
use Illuminate\Support\Facades\DB;

class DeleteCustomerAction
{
    public function execute(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            $customer = Customer::where('id', $customer->id)->lockForUpdate()->firstOrFail();

            if (OutboundTransaction::where('customer_id', $customer->id)->exists()) {
                throw new \Exception("Customer \"{$customer->name}\" tidak dapat dihapus karena masih memiliki transaksi barang keluar.");
            }

            $customer->delete();
        });
    }
}
