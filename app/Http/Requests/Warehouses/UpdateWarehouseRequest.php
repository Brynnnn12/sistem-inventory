<?php

namespace App\Http\Requests\Warehouses;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper(trim($this->code))]);
        }
        if ($this->has('name')) {
            $this->merge(['name' => strip_tags(trim($this->name))]);
        }
        if ($this->has('address')) {
            $this->merge(['address' => strip_tags(trim($this->address))]);
        }
        if ($this->has('phone')) {
            $this->merge(['phone' => trim($this->phone)]);
        }
    }

    public function rules(): array
    {
        $warehouseId = $this->route('warehouse')?->id ?? $this->route('warehouse');

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z0-9\-]+$/',
                'unique:warehouses,code,' . $warehouseId,
            ],
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-]+$/',
            ],
            'address' => [
                'sometimes',
                'required',
                'string',
                'min:10',
                'max:500',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:15',
                'regex:/^[0-9\-\+\(\)\s]+$/',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode gudang tidak boleh dikosongkan.',
            'code.string' => 'Kode gudang harus berupa teks.',
            'code.max' => 'Kode gudang maksimal 10 karakter.',
            'code.unique' => 'Kode gudang sudah digunakan.',
            'code.regex' => 'Kode gudang hanya boleh berisi huruf besar, angka, dan tanda hubung.',

            'name.required' => 'Nama gudang tidak boleh dikosongkan.',
            'name.string' => 'Nama gudang harus berupa teks.',
            'name.max' => 'Nama gudang maksimal 50 karakter.',
            'name.regex' => 'Nama gudang hanya boleh berisi huruf, angka, spasi, dan tanda hubung.',

            'address.required' => 'Alamat gudang tidak boleh dikosongkan.',
            'address.string' => 'Alamat gudang harus berupa teks.',
            'address.min' => 'Alamat gudang minimal 10 karakter.',
            'address.max' => 'Alamat gudang terlalu panjang.',

            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.max' => 'Nomor telepon maksimal 15 karakter.',
            'phone.regex' => 'Format nomor telepon tidak valid.',

            'is_active.boolean' => 'Status aktif harus berupa boolean.',
        ];
    }
}
