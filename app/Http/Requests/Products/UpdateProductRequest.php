<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => strip_tags(trim($this->name)),
            'unit' => trim($this->unit),
            'price' => $this->formatNumber($this->price),
            'cost' => $this->formatNumber($this->cost),
            'min_stock' => $this->formatNumber($this->min_stock),
            'max_stock' => $this->formatNumber($this->max_stock),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Satu fungsi untuk semua kebutuhan angka (Desimal maupun Integer)
     */
    private function formatNumber($value): mixed
    {
        if (is_null($value) || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return $value;
        }

        // Menghapus titik ribuan, mengubah koma menjadi titik desimal
        // Contoh: "1.250.000,00" -> "1250000.00"
        return str_replace(['.', ','], ['', '.'], $value);
    }

    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'name' => 'sometimes|required|string|min:3|max:255|regex:/^[a-zA-Z0-9\s\-\.\(\)]+$/',
            'unit' => 'sometimes|required|string|max:50',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:1|gte:min_stock',
            'price' => 'nullable|integer|min:0',
            'cost' => 'nullable|integer|min:0|lte:price',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori produk wajib diisi.',
            'category_id.integer' => 'Kategori produk tidak valid.',
            'category_id.exists' => 'Kategori produk tidak ditemukan.',
            'name.required' => 'Nama produk wajib diisi.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.min' => 'Nama produk minimal 3 karakter.',
            'name.max' => 'Nama produk tidak boleh lebih dari 255 karakter.',
            'name.regex' => 'Nama produk hanya boleh mengandung huruf, angka, spasi, dan tanda hubung (-), titik, atau kurung.',
            'unit.required' => 'Satuan produk wajib diisi.',
            'unit.string' => 'Satuan produk harus berupa teks.',
            'unit.max' => 'Satuan produk tidak boleh lebih dari 50 karakter.',
            'min_stock.integer' => 'Stok minimum harus berupa angka bulat.',
            'min_stock.min' => 'Stok minimum tidak boleh kurang dari 0.',
            'max_stock.integer' => 'Stok maksimum harus berupa angka bulat.',
            'max_stock.min' => 'Stok maksimum harus lebih dari 0.',
            'max_stock.gte' => 'Stok maksimum harus lebih besar atau sama dengan stok minimum.',
            'price.integer' => 'Harga jual harus berupa angka bulat.',
            'price.min' => 'Harga jual tidak boleh kurang dari 0.',
            'cost.integer' => 'Harga modal harus berupa angka bulat.',
            'cost.min' => 'Harga modal tidak boleh kurang dari 0.',
            'cost.lte' => 'Harga modal tidak boleh lebih besar dari harga jual.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 1000 karakter.',
            'is_active.boolean' => 'Status aktif harus berupa boolean.',
        ];
    }
}
