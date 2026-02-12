<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            // Bersihkan nama dari potensi XSS
            'name' => strip_tags(trim($this->name)),
            'code' => strtoupper(trim($this->code)),
            'unit' => trim($this->unit),
            // Bersihkan angka dari format ribuan (titik/koma)
            'price' => $this->cleanNumber($this->price),
            'cost' => $this->cleanNumber($this->cost),
            'min_stock' => $this->cleanNumber($this->min_stock),
            'max_stock' => $this->cleanNumber($this->max_stock),
        ]);
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\.\(\)]+$/',
            ],
            'unit' => [
                'required',
                'string',
                'max:50',
            ],
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'cost' => [
                'nullable',
                'numeric',
                'min:0',
                'lte:price',
            ],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    private function cleanNumber($value)
    {
        return is_string($value) ? str_replace(['.', ','], '', $value) : $value;
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori produk wajib diisi.',
            'category_id.integer' => 'Kategori produk tidak valid.',
            'category_id.exists' => 'Kategori produk tidak ditemukan.',
            'name.required' => 'Nama produk wajib diisi.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.max' => 'Nama produk tidak boleh lebih dari 255 karakter.',
            'name.regex' => 'Nama produk hanya boleh mengandung huruf, angka, spasi, dan tanda hubung (-), titik, atau kurung.',
            'unit.required' => 'Satuan produk wajib diisi.',
            'unit.string' => 'Satuan produk harus berupa teks.',
            'unit.max' => 'Satuan produk tidak boleh lebih dari 50 karakter.',
            'min_stock.integer' => 'Stok minimum harus berupa angka.',
            'min_stock.min' => 'Stok minimum tidak boleh kurang dari 0.',
            'max_stock.integer' => 'Stok maksimum harus berupa angka.',
            'max_stock.min' => 'Stok maksimum tidak boleh kurang dari 0.',
            'price.numeric' => 'Harga jual harus berupa angka.',
            'price.min' => 'Harga jual tidak boleh kurang dari 0.',
            'cost.numeric' => 'Harga modal harus berupa angka.',
            'cost.min' => 'Harga modal tidak boleh kurang dari 0.',
            'cost.lte' => 'Harga modal tidak boleh lebih besar dari harga jual.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 1000 karakter.',
            'is_active.boolean' => 'Status aktif harus berupa boolean.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPEG, PNG, JPG, GIF, atau WebP.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
        ];
    }
}
