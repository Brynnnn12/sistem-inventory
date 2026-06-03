<?php

declare(strict_types=1);

namespace App\Actions\Stock;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UpdateStockAction
{
    public function __construct(
        private readonly Stock $stock,
        private readonly StockHistory $stockHistory,
    ) {}

    public function execute(
        int $warehouseId,
        int $productId,
        float $quantity,
        string $type,
        int $referenceId,
        string $referenceCode,
        ?string $notes = null,
        ?int $updatedBy = null,
    ): Stock {
        $updatedBy ??= (int) Auth::id();

        return DB::transaction(function () use ($warehouseId, $productId, $quantity, $type, $referenceId, $referenceCode, $notes, $updatedBy) {
            if (! Warehouse::where('id', $warehouseId)->exists()) {
                throw new InvalidArgumentException('Gudang tidak ditemukan');
            }

            if (! Product::where('id', $productId)->exists()) {
                throw new InvalidArgumentException('Produk tidak ditemukan');
            }

            $stock = $this->stock->firstOrCreate(
                [
                    'warehouse_id' => $warehouseId,
                    'product_id' => $productId,
                ],
                [
                    'quantity' => 0,
                    'reserved_qty' => 0,
                    'last_updated' => now(),
                    'updated_by' => $updatedBy,
                ]
            );

            $previousQty = $stock->quantity;
            $newQty = $previousQty + $quantity;

            if ($newQty < 0 && $type !== 'adjustment') {
                throw new InvalidArgumentException('Stok tidak boleh negatif. Stok saat ini: '.(string) $previousQty.', perubahan: '.(string) $quantity);
            }

            $stock->update([
                'quantity' => $newQty,
                'available_qty' => $stock->available_qty + $quantity,
                'last_updated' => now(),
                'updated_by' => $updatedBy,
            ]);

            $this->stockHistory->create([
                'stock_id' => $stock->id,
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'previous_qty' => $previousQty,
                'new_qty' => $newQty,
                'change_qty' => $quantity,
                'reference_type' => $type,
                'reference_id' => $referenceId,
                'reference_code' => $referenceCode,
                'notes' => $notes,
                'created_by' => $updatedBy,
            ]);

            return $stock;
        });
    }
}
