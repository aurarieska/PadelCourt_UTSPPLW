
<?php

namespace Database\Seeders;

use App\Models\JenisLapangan;
use App\Models\Lapangan;
use Illuminate\Database\Seeder;

class LapanganSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Lapangan 1 - Blue Panoramic', 'Panoramic', 350000, 'Lapangan padel indoor berstandar internasional dengan kaca panoramik dan pencahayaan LED profesional.'],
            ['Lapangan 2 - Center Glass', 'Indoor', 320000, 'Lapangan padel indoor dengan dinding kaca penuh, nyaman untuk permainan sepanjang hari.'],
            ['Lapangan 3 - Open Sky Arena', 'Outdoor', 250000, 'Lapangan padel outdoor dengan pemandangan langit terbuka dan sirkulasi udara alami yang segar.'],
        ];

        foreach ($data as [$nama, $jenis, $harga, $deskripsi]) {
            Lapangan::updateOrCreate(
                ['nama_lapangan' => $nama],
                [
                    'id_jenis' => JenisLapangan::where('nama_jenis', $jenis)->value('id_jenis'),
                    'harga_per_sesi' => $harga,
                    'deskripsi' => $deskripsi,
                    'status_lapangan' => 'aktif',
                ]
            );
        }
    }
}
