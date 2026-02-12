<?php

namespace App\Http\Requests\Warehouses;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            // Strip tags untuk mencegah XSS (script injection)
            'code' => strtoupper(trim($this->code)),
            'name' => strip_tags(trim($this->name)),
            // Khusus alamat, kita gunakan strip_tags tapi tetap izinkan karakter umum alamat
            'address' => strip_tags(trim($this->address)),
            'phone' => trim($this->phone),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:10',
                'unique:warehouses,code',
                'regex:/^[A-Z0-9\-]+$/',
            ],
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-]+$/', // Hanya huruf, angka, spasi, dash
            ],
            'address' => [
                'required',
                'string',
                'min:10', // Alamat biasanya panjang, cegah input asal-asalan
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
            'code.required' => 'Kode gudang wajib diisi.',
            'code.string' => 'Kode gudang harus berupa teks.',
            'code.max' => 'Kode gudang maksimal 10 karakter.',
            'code.unique' => 'Kode gudang sudah digunakan.',
            'code.regex' => 'Kode gudang hanya boleh berisi huruf besar, angka, dan tanda hubung.',

            'name.required' => 'Nama gudang wajib diisi.',
            'name.string' => 'Nama gudang harus berupa teks.',
            'name.max' => 'Nama gudang maksimal 50 karakter.',
            'name.regex' => 'Nama gudang hanya boleh berisi huruf, angka, spasi, dan tanda hubung.',

            'address.required' => 'Alamat gudang wajib diisi.',
            'address.string' => 'Alamat gudang harus berupa teks.',
            'address.min' => 'Alamat gudang terlalu pendek (minimal 10 karakter).',
            'address.max' => 'Alamat gudang terlalu panjang.',

            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.max' => 'Nomor telepon maksimal 15 karakter.',
            'phone.regex' => 'Format nomor telepon tidak valid.',

            'is_active.boolean' => 'Status aktif harus berupa boolean.',
        ];
    }
}
