<?php

namespace App\Http\Requests\Opname;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOpnameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'physical_qty' => 'required|numeric|min:0',
            'opname_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'warehouse_id.required' => 'Gudang harus dipilih.',
            'product_id.required' => 'Produk harus dipilih.',
            'physical_qty.required' => 'Jumlah fisik harus diisi.',
            'physical_qty.numeric' => 'Jumlah fisik harus angka.',
            'opname_date.required' => 'Tanggal opname harus diisi.',
            'opname_date.before_or_equal' => 'Tanggal opname tidak boleh di masa depan.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->hasRole('super-admin') && ! $this->has('warehouse_id')) {
            $userWarehouse = $user->warehouses()->first();
            if ($userWarehouse) {
                $this->merge(['warehouse_id' => $userWarehouse->id]);
            }
        }
    }
}
