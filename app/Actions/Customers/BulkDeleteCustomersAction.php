<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class BulkDeleteCustomersAction
{
    /**
     * Bulk delete customers (soft delete).
     *
     * @param  array<int>  $ids
     *
     * @throws \Exception
     */
    public function execute(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            // Lock for update to prevent race conditions
            $customers = Customer::whereIn('id', $ids)->lockForUpdate()->get();

            // Soft delete - preserves audit trail
            return Customer::whereIn('id', $ids)->delete();
        });
    }
}
