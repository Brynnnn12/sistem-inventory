<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteProductAction
{
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
                Storage::disk('public')->delete($product->image);
            }

            // Soft delete - preserves audit trail for stock_logs and stock_transfers
            $product->delete();
        });
    }
}
