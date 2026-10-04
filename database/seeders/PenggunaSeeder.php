<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Admin PadelCourt', 'admin@padelcourt.com', '081200000001', 'admin', 'Adm1n!Court26'],
            ['Budi Santoso', 'budi.santoso@gmail.com', '081200000002', 'pelanggan', 'BudiMain#2610'],
            ['Citra Lestari', 'citra.lestari@gmail.com', '081200000003', 'pelanggan', 'Citra*Padel07'],
        ];

        foreach ($data as [$nama, $email, $telepon, $role, $password]) {
            Pengguna::updateOrCreate(
                ['email' => $email],
                [
                    'nama_pengguna' => $nama,
                    'password' => $password, // di-hash otomatis oleh cast 'hashed' pada model
                    'no_telepon' => $telepon,
                    'role' => $role,
                ]
            );
        }
    }
}
