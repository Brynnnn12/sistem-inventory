<?php

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

    //ini akan generate kode gudang secara otomatis
    private function generateWarehouseCode(): string
    {
        do {
            $code = 'WH-'.str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Warehouse::where('code', $code)->exists());

        return $code;
    }
}
