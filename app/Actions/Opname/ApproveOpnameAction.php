<?php

declare(strict_types=1);

namespace App\Actions\Opname;

use App\Actions\Stock\UpdateStockAction;
use App\Models\Opname;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ApproveOpnameAction
{
    public function __construct(
        private readonly UpdateStockAction $updateStockAction,
    ) {}

    public function execute(int $opnameId): Opname
    {
        return DB::transaction(function () use ($opnameId) {
            $opname = Opname::findOrFail($opnameId);

            if ($opname->status !== 'draft') {
                throw new InvalidArgumentException('Opname hanya bisa diapprove jika status draft.');
            }

            if ($opname->stockHistories()->exists()) {
                throw new InvalidArgumentException('Opname sudah diapprove sebelumnya.');
            }

            if ($opname->difference_type !== 'sama') {
                $adjustmentQty = $opname->difference_type === 'lebih'
                    ? (float) $opname->difference_qty
                    : -(float) $opname->difference_qty;

                $this->updateStockAction->execute(
                    warehouseId: $opname->warehouse_id,
                    productId: $opname->product_id,
                    quantity: $adjustmentQty,
                    type: 'adjustment',
                    referenceId: $opname->id,
                    referenceCode: $opname->code,
                    notes: "Stock adjustment from opname: {$opname->code}",
                );
            }

            $opname->update(['status' => 'approved']);

            return $opname->load(['warehouse', 'product', 'creator', 'stockHistories']);
        });
    }
}
