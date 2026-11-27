<?php

namespace Database\Seeders;

use App\Models\JenisLapangan;
use Illuminate\Database\Seeder;

class JenisLapanganSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Indoor' => 'Lapangan tertutup, bebas hujan dan panas.',
            'Outdoor' => 'Lapangan terbuka dengan sirkulasi udara alami.',
            'Panoramic' => 'Lapangan dengan dinding kaca penuh, pandangan 360 derajat.',
            'Semi-outdoor' => 'Lapangan beratap dengan sisi terbuka.',
        ];

        foreach ($data as $nama => $keterangan) {
            JenisLapangan::updateOrCreate(['nama_jenis' => $nama], ['keterangan' => $keterangan]);
        }
    }
}

