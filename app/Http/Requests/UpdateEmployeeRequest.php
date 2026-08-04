<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {

        return $this->user()->can('update', $this->route('employee'));
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('name')) {
            $merge['name'] = strip_tags(trim($this->name));
        }

        if ($this->has('email')) {
            $merge['email'] = strtolower(trim($this->email));
        }

        if (! empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        // Mengambil model employee dari route
        $employee = $this->route('employee');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:50', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email:dns',
                'max:50',
                Rule::unique('users')->ignore($employee->id)->whereNull('deleted_at'),
            ],
            'phone_number' => ['sometimes', 'required', 'string', 'regex:/^628[0-9]{9,11}$/'],
            'role' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['super-admin', 'admin', 'viewer']),
                function ($attribute, $value, $fail) {
                    $employee = $this->route('employee');

                    if ($value !== 'viewer') {
                        return;
                    }

                    if (! $employee->hasAnyRole(['super-admin', 'admin'])) {
                        return;
                    }

                    $warehouseCount = $employee->warehouses()->count();
                    if ($warehouseCount > 0) {
                        $fail("Karyawan dengan peran admin masih ditugaskan di {$warehouseCount} gudang. Hapus penugasan gudang terlebih dahulu sebelum mengubah peran menjadi viewer.");
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.regex' => 'Nama hanya boleh berisi huruf.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'phone_number.regex' => 'Nomor HP harus format Indonesia (628...) dan panjang 12-14 digit.',
        ];
    }
}
