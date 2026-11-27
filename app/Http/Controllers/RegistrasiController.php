<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrasiController extends Controller
{
    public function tampil(): View
    {
        return view('admin.auth.register');
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_pengguna' => 'required|string|min:3|max:150',
            'email' => 'required|email|max:100|unique:pengguna,email',
            'no_telepon' => ['required', 'regex:/^[0-9+]{8,15}$/'],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'Email sudah terdaftar.',
            'no_telepon.regex' => 'No. telepon hanya angka (8-15 digit).',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        // Pendaftar publik selalu menjadi pelanggan (tidak bisa memilih role)
        Pengguna::create([
            'nama_pengguna' => $data['nama_pengguna'],
            'email' => $data['email'],
            'no_telepon' => $data['no_telepon'],
            'password' => $data['password'],
            'id_role' => Role::where('nama_role', 'pelanggan')->value('id_role'),
        ]);

        return redirect()->route('login')->with('sukses', 'Registrasi berhasil, silakan login.');
    }
}
