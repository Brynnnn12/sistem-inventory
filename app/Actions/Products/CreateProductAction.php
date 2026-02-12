<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateProductAction
{
    /**
     * Create a new product.
     *
     * @param  array<string, mixed>  $input
     */
    public function execute(array $input): Product
    {
        return DB::transaction(function () use ($input) {
            $imagePath = null;

            // Handle image upload
            if (isset($input['image']) && $input['image'] instanceof UploadedFile) {
                $imagePath = $this->storeImage($input['image']);
            }

            // Generate unique product code
            $code = $this->generateProductCode();

            // Create product
            $product = Product::create([
                'code' => $code,
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
        });
    }

    /**
     * Generate unique product code.
     */
    private function generateProductCode(): string
    {
        do {
            $code = 'PRD-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Product::where('code', $code)->exists());

        return $code;
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
