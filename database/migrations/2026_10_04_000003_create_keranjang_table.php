<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->increments('id_keranjang');
            $table->unsignedInteger('id_pengguna');
            $table->unsignedInteger('id_lapangan');
            $table->date('tanggal_main');
            $table->time('jam_mulai');
            $table->integer('harga_satuan'); // snapshot harga saat ditambahkan
            $table->timestamp('dibuat_pada')->useCurrent();

            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->cascadeOnDelete();
            $table->foreign('id_lapangan')->references('id_lapangan')->on('lapangan')->restrictOnDelete();

            // sesi yang sama tidak boleh masuk keranjang dua kali
            $table->unique(['id_pengguna', 'id_lapangan', 'tanggal_main', 'jam_mulai'], 'uq_keranjang_sesi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};
