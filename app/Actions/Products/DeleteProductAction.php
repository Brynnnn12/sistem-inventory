<?php

namespace App\Actions\Products;

use App\Models\Product;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class DeleteProductAction
{
    public function __construct(
        private readonly FileUploadService $fileUploadService,
    ) {}

    /**
     * Delete a product (soft delete).
     *
     * @throws \Exception
     */
    public function execute(Product $product): void
    {
        DB::transaction(function () use ($product) {
            // Lock for update to prevent race conditions
            $product = Product::where('id', $product->id)->lockForUpdate()->firstOrFail();

            // Delete associated image if exists
            if ($product->image) {
                $this->fileUploadService->delete($product->image);
            }

            // Soft delete - preserves audit trail for stock_logs and stock_transfers
            $product->delete();
        });
    }
}
