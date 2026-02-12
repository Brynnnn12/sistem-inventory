<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateProductAction
{
    /**
     * Update an existing product.
     *
     * @param  array<string, mixed>  $input
     */
    public function execute(Product $product, array $input): Product
    {
        $imagePath = $product->image;

        // Handle image upload
        if (isset($input['image'])) {
            if ($input['image'] instanceof UploadedFile) {
                // Delete old image if exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                // Store new image
                $imagePath = $this->storeImage($input['image']);
            } elseif ($input['image'] === null) {
                // Remove image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = null;
            }
        }

        $product->update([
            'code' => $input['code'],
            'category_id' => $input['category_id'],
            'name' => $input['name'],
            'unit' => $input['unit'],
            'min_stock' => $input['min_stock'] ?? 0,
            'max_stock' => $input['max_stock'] ?? 0,
            'price' => $input['price'] ?? 0,
            'cost' => $input['cost'] ?? 0,
            'description' => $input['description'] ?? null,
            'is_active' => $input['is_active'] ?? true,
            'image' => $imagePath,
        ]);

        return $product;
    }

    /**
     * Store uploaded image and return the path.
     */
    private function storeImage(UploadedFile $image): string
    {
        // Generate unique filename
        $filename = 'product_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

        // Store in public disk under products folder
        return $image->storeAs('products', $filename, 'public');
    }
}
