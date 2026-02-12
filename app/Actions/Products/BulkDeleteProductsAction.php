<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BulkDeleteProductsAction
{
    /**
     * Bulk delete products (soft delete).
     *
     * @param  array<int>  $ids
     *
     * @throws \Exception
     */
    public function execute(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            // Lock for update to prevent race conditions
            $products = Product::whereIn('id', $ids)->lockForUpdate()->get();


            // Delete associated images
            foreach ($products as $product) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
            }

            // Soft delete - preserves audit trail
            return Product::whereIn('id', $ids)->delete();
        });
    }
}
