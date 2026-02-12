<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class DeleteCustomerAction
{
    /**
     * Delete a customer (soft delete).
     *
     * @throws \Exception
     */
    public function execute(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            // Lock for update to prevent race conditions
            $customer = Customer::where('id', $customer->id)->lockForUpdate()->firstOrFail();

            // Soft delete - preserves audit trail
            $customer->delete();
        });
    }
}
