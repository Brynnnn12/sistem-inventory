<?php

declare(strict_types=1);

namespace App\Actions\Transaction;

use App\Actions\Stock\UpdateStockAction;
use App\Actions\Transaction\Concerns\GeneratesTransactionCode;
use App\Models\Stock;
use App\Models\StockMutation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class CreateMutationAction
{
    use GeneratesTransactionCode;

    private const PREFIX = 'MT';

    public function __construct(
        private readonly UpdateStockAction $updateStockAction,
        private readonly StockMutation $stockMutation,
    ) {}

    public function send(
        int $fromWarehouseId,
        int $toWarehouseId,
        int $productId,
        float $quantity,
        ?string $notes = null,
        ?int $createdBy = null,
    ): StockMutation {
        $this->validateSendInputs($fromWarehouseId, $toWarehouseId, $quantity);

        $createdBy ??= (int) Auth::id();

        return DB::transaction(function () use ($fromWarehouseId, $toWarehouseId, $productId, $quantity, $notes, $createdBy) {
            $stock = Stock::where('warehouse_id', $fromWarehouseId)
                ->where('product_id', $productId)
                ->first();

            $available = $stock ? $stock->available_qty : 0;

            if ($available < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok gudang asal tidak cukup. Tersedia: '.$available.', diminta: '.$quantity,
                ]);
            }

            $code = $this->generateTransactionCode(self::PREFIX, $this->stockMutation);

            $mutation = $this->stockMutation->create([
                'code' => $code,
                'from_warehouse' => $fromWarehouseId,
                'to_warehouse' => $toWarehouseId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'received_qty' => 0,
                'damaged_qty' => 0,
                'status' => 'sent',
                'sent_at' => now(),
                'notes' => $notes,
                'created_by' => $createdBy,
            ]);

            // Stock not deducted yet — will be deducted on receive/approve

            return $mutation->load(['fromWarehouse', 'toWarehouse', 'product', 'creator']);
        });
    }

    private function validateSendInputs(
        int $fromWarehouseId,
        int $toWarehouseId,
        float $quantity,
    ): void {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Jumlah harus lebih besar dari 0');
        }

        if ($fromWarehouseId === $toWarehouseId) {
            throw new InvalidArgumentException('Gudang asal dan tujuan tidak boleh sama');
        }
    }

    public function receive(
        int $mutationId,
        float $receivedQty,
        float $damagedQty = 0,
        ?int $receivedBy = null,
    ): StockMutation {
        $receivedBy ??= (int) Auth::id();

        return DB::transaction(function () use ($mutationId, $receivedQty, $damagedQty, $receivedBy) {
            $mutation = $this->stockMutation->findOrFail($mutationId);

            if ($mutation->status_display !== 'sent') {
                throw new InvalidArgumentException('Mutation sudah diterima atau tidak valid untuk diterima');
            }

            $totalReceived = $receivedQty + $damagedQty;

            if ($totalReceived > $mutation->quantity) {
                throw ValidationException::withMessages([
                    'received_qty' => 'Jumlah diterima melebihi quantity mutation: '.$mutation->quantity,
                ]);
            }

            // Check source stock availability before deducting
            $sourceStock = Stock::where('warehouse_id', $mutation->from_warehouse)
                ->where('product_id', $mutation->product_id)
                ->first();

            $available = $sourceStock ? $sourceStock->available_qty : 0;

            if ($available < $mutation->quantity) {
                throw ValidationException::withMessages([
                    'received_qty' => 'Stok gudang asal tidak cukup untuk menyelesaikan mutasi. Tersedia: '.$available.', dibutuhkan: '.$mutation->quantity,
                ]);
            }

            $mutation->update([
                'received_qty' => $receivedQty,
                'damaged_qty' => $damagedQty,
                'status' => 'completed',
                'received_at' => now(),
                'received_by' => $receivedBy,
            ]);

            // Deduct full quantity from source warehouse
            $this->updateStockAction->execute(
                warehouseId: $mutation->from_warehouse,
                productId: $mutation->product_id,
                quantity: -$mutation->quantity,
                type: 'mutation_sent',
                referenceId: $mutation->id,
                referenceCode: $mutation->code,
                notes: "Mutation completed: {$mutation->code}",
                updatedBy: $receivedBy,
            );

            // Add received stock to destination warehouse
            if ($receivedQty > 0) {
                $this->updateStockAction->execute(
                    warehouseId: $mutation->to_warehouse,
                    productId: $mutation->product_id,
                    quantity: $receivedQty,
                    type: 'mutation_received',
                    referenceId: $mutation->id,
                    referenceCode: $mutation->code,
                    notes: "Mutation received: {$mutation->code}",
                    updatedBy: $receivedBy,
                );
            }

            return $mutation->load(['fromWarehouse', 'toWarehouse', 'product', 'creator', 'receiver']);
        });
    }

    public function reject(
        int $mutationId,
        ?string $notes = null,
        ?int $rejectedBy = null,
    ): StockMutation {
        $rejectedBy ??= (int) Auth::id();

        return DB::transaction(function () use ($mutationId, $notes, $rejectedBy) {
            $mutation = $this->stockMutation->findOrFail($mutationId);

            if ($mutation->status_display !== 'sent') {
                throw new InvalidArgumentException('Mutation sudah diproses atau tidak valid untuk ditolak');
            }

            $data = [
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejected_by' => $rejectedBy,
            ];

            if ($notes !== null) {
                $data['notes'] = $notes;
            }

            $mutation->update($data);

            // Stock was never deducted on send, so no need to return it

            return $mutation->load(['fromWarehouse', 'toWarehouse', 'product', 'creator']);
        });
    }
}
