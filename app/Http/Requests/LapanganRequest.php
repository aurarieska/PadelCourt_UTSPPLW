<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LapanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses sudah dijaga middleware role:admin di route
    }

    // Input harga bisa ditulis menjadi "350.000", titiknya dibuang dulu sebelum validasi
    protected function prepareForValidation(): void
    {
        $this->merge([
            'harga_per_sesi' => preg_replace('/\D/', '', (string) $this->input('harga_per_sesi')),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_lapangan' => ['required', 'string', 'max:100'],
            'jenis_lapangan' => ['required', 'in:indoor,outdoor'],
            'deskripsi' => ['nullable', 'string'],
            'harga_per_sesi' => ['required', 'integer', 'min:1', 'max:99999999'],
            'foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'], // 5 MB
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lapangan.required' => 'Nama lapangan wajib diisi.',
            'nama_lapangan.max' => 'Nama lapangan maksimal 100 karakter.',
            'jenis_lapangan.required' => 'Tipe lapangan wajib dipilih.',
            'jenis_lapangan.in' => 'Tipe lapangan harus indoor atau outdoor.',
            'harga_per_sesi.required' => 'Harga per sesi wajib diisi.',
            'harga_per_sesi.integer' => 'Harga per sesi harus berupa angka.',
            'harga_per_sesi.min' => 'Harga per sesi harus lebih dari 0.',
            'harga_per_sesi.max' => 'Harga per sesi terlalu besar.',
            'foto.mimes' => 'Foto harus berformat JPG atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
            'foto.uploaded' => 'Foto gagal diunggah. Pastikan ukurannya tidak lebih dari 5 MB.',
        ];
    }
}
