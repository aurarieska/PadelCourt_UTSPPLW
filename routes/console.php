<?php

use App\Models\Pesanan;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan pesanan daring yang melewati batas bayar (30 menit) dan buka kembali jadwalnya
Artisan::command('pesanan:batalkan-kedaluwarsa', function () {
    $jumlah = 0;

    $kandidat = Pesanan::where('status_pesanan', Pesanan::STATUS_MENUNGGU_PEMBAYARAN)
        ->whereNotNull('batas_bayar')
        ->where('batas_bayar', '<', now())
        ->pluck('id_pesanan');

    foreach ($kandidat as $id) {
        DB::transaction(function () use ($id, &$jumlah) {
            $p = Pesanan::lockForUpdate()->find($id);

            if ($p && $p->status_pesanan === Pesanan::STATUS_MENUNGGU_PEMBAYARAN) {
                $p->update(['status_pesanan' => Pesanan::STATUS_BATAL, 'alasan_batal' => 'kedaluwarsa']);
                $p->detail()->update(['aktif' => false]);
                $jumlah++;
            }
        });
    }

    $this->info("{$jumlah} pesanan dibatalkan.");
})->purpose('Batalkan pesanan yang melewati batas pembayaran');

Schedule::command('pesanan:batalkan-kedaluwarsa')->everyMinute();
