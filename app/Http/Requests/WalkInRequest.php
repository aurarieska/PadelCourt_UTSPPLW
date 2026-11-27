<?php

namespace App\Http\Requests;

use App\Models\DetailPesanan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WalkInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // no telepon disambung, tidak memisah "0812 3456 7890" disimpan sebagai "081234567890"
    protected function prepareForValidation(): void
    {
        $this->merge([
            'no_telepon_pemesan' => preg_replace('/[\s\-]/', '', (string) $this->input('no_telepon_pemesan')),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_pemesan' => ['required', 'string', 'max:150'],
            'no_telepon_pemesan' => ['required', 'regex:/^\+?[0-9]{8,14}$/'],
            'metode_pembayaran' => ['required', Rule::in(['tunai', 'QRIS'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_lapangan' => ['required', 'integer', 'exists:lapangan,id_lapangan'],
            'items.*.tanggal' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'items.*.jam_mulai' => ['required', Rule::in(DetailPesanan::jamSesi())],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pemesan.required' => 'Nama customer wajib diisi.',
            'nama_pemesan.max' => 'Nama customer maksimal 150 karakter.',
            'no_telepon_pemesan.required' => 'No. HP wajib diisi.',
            'no_telepon_pemesan.regex' => 'No. HP tidak valid (8-14 digit angka, boleh diawali +).',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran harus tunai atau QRIS.',
            'items.required' => 'Tambahkan minimal satu sesi ke daftar pesanan.',
            'items.min' => 'Tambahkan minimal satu sesi ke daftar pesanan.',
            'items.*.id_lapangan.exists' => 'Lapangan yang dipilih tidak ditemukan.',
            'items.*.tanggal.after_or_equal' => 'Tanggal bermain tidak boleh sebelum hari ini.',
            'items.*.jam_mulai.in' => 'Jam sesi harus antara 08:00 dan 21:00.',
        ];
    }
}
