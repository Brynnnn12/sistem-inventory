<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan logic Policy sudah terdaftar di AuthServiceProvider
        return $this->user()->can('create', User::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => strip_tags(trim($this->name)),
            // Email dipaksa huruf kecil semua untuk konsistensi data
            'email' => strtolower(trim($this->email)),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => [
                'required',
                'string',
                'email:dns',
                'max:50',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'phone_number' => ['required', 'string', 'regex:/^628[0-9]{9,11}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:super-admin,admin,viewer'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Nama hanya boleh berisi huruf.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'phone_number.regex' => 'Nomor telepon harus diawali dengan 628 dan diikuti 9-11 digit angka.',
            'password.min' => 'Password harus memiliki minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.in' => 'Peran yang dipilih tidak valid.',

        ];
    }
}
