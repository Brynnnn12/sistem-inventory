<?php

declare(strict_types=1);

namespace App\Actions\Stock;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use InvalidArgumentException;

class CheckStockAvailabilityAction
{
    public function __construct(
        private readonly Stock $stock,
    ) {}

    public function execute(int $warehouseId, int $productId, float $requestedQty): bool
    {
        if (! Warehouse::where('id', $warehouseId)->exists()) {
            throw new InvalidArgumentException('Gudang tidak ditemukan');
        }

        if (! Product::where('id', $productId)->exists()) {
            throw new InvalidArgumentException('Produk tidak ditemukan');
        }

        if ($requestedQty <= 0) {
            throw new InvalidArgumentException('Jumlah diminta harus lebih besar dari 0');
        }

        $stock = $this->stock
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->first();

        if (! $stock) {
            return false;
        }

        return $stock->available_qty >= $requestedQty;
    }

    public function getStockInfo(int $warehouseId, int $productId): array
    {
        if (! Warehouse::where('id', $warehouseId)->exists()) {
            throw new InvalidArgumentException('Gudang tidak ditemukan');
        }

        if (! Product::where('id', $productId)->exists()) {
            throw new InvalidArgumentException('Produk tidak ditemukan');
        }

        $stock = $this->stock
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->first();

        if (! $stock) {
            return [
                'available' => 0.0,
                'current' => 0.0,
                'reserved' => 0.0,
                'is_available' => false,
            ];
        }

        return [
            'available' => $stock->available_qty,
            'current' => $stock->quantity,
            'is_available' => true,
        ];
    }
}
