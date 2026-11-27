<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JenisLapanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses dibatasi middleware pada route
    }

    public function rules(): array
    {
        return [
            'nama_jenis' => [
                'required', 'string', 'min:3', 'max:50',
                Rule::unique('jenis_lapangan', 'nama_jenis')->ignore($this->route('jenis'), 'id_jenis'),
            ],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_jenis.required' => 'Nama jenis wajib diisi.',
            'nama_jenis.min' => 'Nama jenis minimal 3 karakter.',
            'nama_jenis.unique' => 'Nama jenis sudah ada.',
        ];
    }
}
