<?php

declare(strict_types=1);

namespace App\Actions\Transaction;

use App\Actions\Stock\CheckStockAvailabilityAction;
use App\Actions\Stock\UpdateStockAction;
use App\Actions\Transaction\Concerns\GeneratesTransactionCode;
use App\Models\OutboundTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class CreateOutboundTransactionAction
{
    use GeneratesTransactionCode;

    private const PREFIX = 'BK';

    public function __construct(
        private readonly UpdateStockAction $updateStockAction,
        private readonly CheckStockAvailabilityAction $checkStockAvailabilityAction,
        private readonly OutboundTransaction $outboundTransaction,
    ) {}

    public function execute(
        int $customerId,
        int $warehouseId,
        int $productId,
        float $quantity,
        float $unitPrice,
        string $saleDate,
        ?string $notes = null,
        ?int $createdBy = null,
    ): OutboundTransaction {
        $this->validateInputs($quantity, $unitPrice, $saleDate);

        $createdBy ??= (int) Auth::id();

        return DB::transaction(function () use ($customerId, $warehouseId, $productId, $quantity, $unitPrice, $saleDate, $notes, $createdBy) {
            $stockInfo = $this->checkStockAvailabilityAction->getStockInfo($warehouseId, $productId);

            if (! $stockInfo['is_available'] || $stockInfo['available'] < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok tidak cukup. Tersedia: {$stockInfo['available']}, diminta: {$quantity}",
                ]);
            }

            $code = $this->generateTransactionCode(self::PREFIX, $this->outboundTransaction);

            $transaction = $this->outboundTransaction->create([
                'code' => $code,
                'customer_id' => $customerId,
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'sale_date' => $saleDate,
                'notes' => $notes,
                'created_by' => $createdBy,
            ]);

            $this->updateStockAction->execute(
                warehouseId: $warehouseId,
                productId: $productId,
                quantity: -$quantity,
                type: 'outbound',
                referenceId: $transaction->id,
                referenceCode: $code,
                notes: "Outbound transaction: {$code}",
            );

            return $transaction->load(['customer', 'warehouse', 'product', 'creator']);
        });
    }

    private function validateInputs(
        float $quantity,
        float $unitPrice,
        string $saleDate,
    ): void {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Jumlah harus lebih besar dari 0');
        }

        if ($unitPrice < 0) {
            throw new InvalidArgumentException('Harga satuan tidak boleh negatif');
        }

        if (! strtotime($saleDate)) {
            throw new InvalidArgumentException('Tanggal penjualan tidak valid');
        }
    }
}
