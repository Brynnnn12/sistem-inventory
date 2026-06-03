<?php

declare(strict_types=1);

namespace App\Actions\Warehouses;

use App\Models\Warehouse;

class CreateWarehouseAction
{
    /**
     * Create a new warehouse.
     *
     * @param  array<string, mixed>  $input
     */
    public function execute(array $input): Warehouse
    {
        $input['code'] = $this->generateWarehouseCode();

        return Warehouse::create($input);
    }

    private function generateWarehouseCode(): string
    {
        do {
            $code = 'WH-'.str_pad((string) mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Warehouse::where('code', $code)->exists());

        return $code;
    }
}
