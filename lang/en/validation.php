<?php

return [
    'required' => ':attribute wajib diisi.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'string' => ':attribute harus berupa teks.',
    'min' => [
        'string' => ':attribute minimal :min karakter.',
    ],
    'max' => [
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'unique' => ':attribute sudah digunakan.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'attributes' => [
        'email' => 'Email',
        'password' => 'Password',
        'name' => 'Nama',
    ],
];
