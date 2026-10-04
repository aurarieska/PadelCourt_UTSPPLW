<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->increments('id_transaksi');
            $table->unsignedInteger('id_pesanan')->unique(); // maksimal 1 transaksi per pesanan
            $table->unsignedInteger('id_admin')->nullable(); // kosong sebelum diverifikasi
            $table->timestamp('tanggal_bayar');
            $table->string('metode_pembayaran', 10);
            $table->integer('jumlah_bayar');
            $table->string('bukti_bayar', 255)->nullable(); // kosong untuk walk-in
            $table->timestamp('tanggal_verifikasi')->nullable();

            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan');
            $table->foreign('id_admin')->references('id_pengguna')->on('pengguna');
        });

        DB::statement("ALTER TABLE transaksi ADD CONSTRAINT chk_transaksi_metode CHECK (metode_pembayaran IN ('QRIS', 'tunai'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
