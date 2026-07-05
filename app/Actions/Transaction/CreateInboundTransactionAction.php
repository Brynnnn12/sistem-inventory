<?php

declare(strict_types=1);

namespace App\Actions\Transaction;

use App\Actions\Stock\UpdateStockAction;
use App\Actions\Transaction\Concerns\GeneratesTransactionCode;
use App\Models\InboundTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreateInboundTransactionAction
{
    use GeneratesTransactionCode;

    private const PREFIX = 'BM';

    public function __construct(
        private readonly UpdateStockAction $updateStockAction,
        private readonly InboundTransaction $inboundTransaction,
    ) {}

    public function execute(
        int $supplierId,
        int $warehouseId,
        int $productId,
        float $quantity,
        float $unitPrice,
        string $receivedDate,
        ?string $notes = null,
        ?int $createdBy = null,
    ): InboundTransaction {
        $this->validateInputs($quantity, $unitPrice, $receivedDate);

        $createdBy ??= (int) Auth::id();

        return DB::transaction(function () use ($supplierId, $warehouseId, $productId, $quantity, $unitPrice, $receivedDate, $notes, $createdBy) {
            $code = $this->generateTransactionCode(self::PREFIX, $this->inboundTransaction);

            $transaction = $this->inboundTransaction->create([
                'code' => $code,
                'supplier_id' => $supplierId,
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'received_date' => $receivedDate,
                'notes' => $notes,
                'created_by' => $createdBy,
            ]);

            $this->updateStockAction->execute(
                warehouseId: $warehouseId,
                productId: $productId,
                quantity: $quantity,
                type: 'inbound',
                referenceId: $transaction->id,
                referenceCode: $code,
                notes: "Inbound transaction: {$code}",
            );

            return $transaction->load(['supplier', 'warehouse', 'product', 'creator']);
        });
    }

    private function validateInputs(
        float $quantity,
        float $unitPrice,
        string $receivedDate,
    ): void {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Jumlah harus lebih besar dari 0');
        }

        if ($unitPrice < 0) {
            throw new InvalidArgumentException('Harga satuan tidak boleh negatif');
        }

        if (! strtotime($receivedDate)) {
            throw new InvalidArgumentException('Tanggal diterima tidak valid');
        }
    }
}
