<?php

declare(strict_types=1);

namespace App\Actions\Opname;

use App\Models\Opname;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOpnameAction
{
    public function __construct(private readonly Opname $opname) {}

    /**
     * Create an opname record.
     *
     * @param  array<string,mixed>  $input
     */
    public function execute(
        array $input,
        ?int $createdBy = null,
    ): Opname {
        $createdBy ??= (int) Auth::id();

        return DB::transaction(function () use ($input, $createdBy) {
            $warehouseId = (int) $input['warehouse_id'];
            $productId = (int) $input['product_id'];
            $physicalQty = (float) $input['physical_qty'];
            $opnameDate = $input['opname_date'];
            $notes = $input['notes'] ?? null;

            $exists = $this->opname->where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->whereDate('opname_date', $opnameDate)
                ->whereNotIn('status', ['rejected'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'product_id' => 'Opname untuk produk ini pada tanggal yang sama sudah ada.',
                ]);
            }

            $stock = \App\Models\Stock::where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->first();

            $systemQty = $stock ? (float) $stock->quantity : 0;
            $differenceQty = $physicalQty - $systemQty;
            $differenceType = $differenceQty > 0 ? 'lebih' : ($differenceQty < 0 ? 'kurang' : 'sama');

            $date = now()->format('Ymd');
            $prefix = 'OP';

            $lastOpname = $this->opname->where('code', 'like', "{$prefix}-{$date}-%")
                ->orderBy('code', 'desc')
                ->first();

            $newNumber = $lastOpname
                ? (int) substr($lastOpname->code, -3) + 1
                : 1;

            $code = sprintf('%s-%s-%03d', $prefix, $date, $newNumber);

            return $this->opname->create([
                'code' => $code,
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'system_qty' => $systemQty,
                'physical_qty' => $physicalQty,
                'difference_qty' => abs($differenceQty),
                'difference_type' => $differenceType,
                'status' => 'draft',
                'notes' => $notes,
                'opname_date' => $opnameDate,
                'created_by' => $createdBy,
            ]);
        });
    }
}
