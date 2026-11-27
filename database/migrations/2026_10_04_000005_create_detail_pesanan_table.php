<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->increments('id_detail_pesanan');
            $table->unsignedInteger('id_pesanan');
            $table->unsignedInteger('id_lapangan');
            $table->date('tanggal_main');
            $table->time('jam_mulai');
            $table->integer('harga_satuan'); // snapshot, sekaligus subtotal
            $table->boolean('aktif')->default(true);

            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan');
            $table->foreign('id_lapangan')->references('id_lapangan')->on('lapangan')->restrictOnDelete();
        });

        // Anti double booking: hanya sesi aktif yang diperiksa (sesi pesanan batal boleh dipesan lagi)
        DB::statement('CREATE UNIQUE INDEX uq_jadwal_aktif ON detail_pesanan (id_lapangan, tanggal_main, jam_mulai) WHERE aktif = true');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS uq_jadwal_aktif');
        Schema::dropIfExists('detail_pesanan');
    }
};
