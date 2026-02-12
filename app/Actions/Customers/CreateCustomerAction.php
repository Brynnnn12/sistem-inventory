<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CreateCustomerAction
{
    /**
     * Create a new customer.
     *
     * @param  array<string, mixed>  $input
     */
    public function execute(array $input): Customer
    {
        return DB::transaction(function () use ($input) {
            // Generate unique customer code if not provided
            $code = $input['code'] ?? $this->generateCustomerCode();

            // Create customer
            $customer = Customer::create([
                'code' => $code,
                'name' => $input['name'],
                'contact_person' => $input['contact_person'],
                'phone' => $input['phone'],
                'email' => $input['email'],
                'address' => $input['address'],
                'is_active' => $input['is_active'] ?? true,
            ]);

            return $customer;
        });
    }

    /**
     * Generate unique customer code.
     */
    private function generateCustomerCode(): string
    {
        do {
            $code = 'CST-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Customer::where('code', $code)->exists());

        return $code;
    }
}
