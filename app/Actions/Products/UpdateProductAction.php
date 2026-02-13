<?php

namespace App\Actions\Products;

use App\Models\Product;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;

class UpdateProductAction
{
    public function __construct(
        private readonly FileUploadService $fileUploadService,
    ) {}

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
                    $this->fileUploadService->delete($product->image);
                }
                // Upload new image
                $imagePath = $this->fileUploadService->upload(
                    file: $input['image'],
                    folder: 'products',
                    disk: 'public',
                    allowedMimes: ['image/jpeg', 'image/png'],
                    maxSize: 2048, // 2MB
                    prefix: 'product'
                );
            } elseif ($input['image'] === null) {
                // Remove image
                if ($product->image) {
                    $this->fileUploadService->delete($product->image);
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
}
