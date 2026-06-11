<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreInboundRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    $productId = $this->input('product_id');
                    $warehouseId = $this->input('warehouse_id');
                    if (! $productId || ! $warehouseId) {
                        return;
                    }

                    $product = \App\Models\Product::find($productId);
                    if (! $product || $product->max_stock <= 0) {
                        return;
                    }

                    $currentStock = (float) (\App\Models\Stock::where('product_id', $productId)
                        ->where('warehouse_id', $warehouseId)
                        ->value('quantity') ?? 0);

                    $newTotal = $currentStock + (float) $value;

                    if ($newTotal > $product->max_stock) {
                        $fail("Jumlah melebihi stok maksimum ({$product->max_stock} {$product->unit}). Stok saat ini: {$currentStock} {$product->unit}.");
                    }
                },
            ],
            'unit_price' => 'nullable|numeric|min:0',
            'received_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'warehouse_id.required' => 'Gudang harus dipilih.',
            'supplier_id.required' => 'Supplier harus dipilih.',
            'product_id.required' => 'Produk harus dipilih.',
            'quantity.required' => 'Jumlah harus diisi.',
            'quantity.min' => 'Jumlah harus lebih dari 0.',
            'received_date.required' => 'Tanggal penerimaan harus diisi.',
            'received_date.before_or_equal' => 'Tanggal penerimaan tidak boleh di masa depan.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set warehouse_id based on user role
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! $user->hasRole('super-admin') && ! $this->has('warehouse_id')) {
            $userWarehouse = $user->warehouses()->where('is_primary', true)->first()
                ?? $user->warehouses()->first();
            if ($userWarehouse) {
                $this->merge(['warehouse_id' => $userWarehouse->id]);
            }
        }
    }
}
