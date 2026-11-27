<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_lapangan', function (Blueprint $table) {
            $table->increments('id_jenis');
            $table->string('nama_jenis', 50)->unique();
            $table->text('keterangan')->nullable();
        });

        DB::table('jenis_lapangan')->insert([
            ['nama_jenis' => 'Indoor', 'keterangan' => 'Lapangan tertutup, bebas hujan dan panas.'],
            ['nama_jenis' => 'Outdoor', 'keterangan' => 'Lapangan terbuka dengan sirkulasi udara alami.'],
        ]);

        // Kolom lama jenis_lapangan sengaja DIPERTAHANKAN dulu agar form lapangan
        // yang sekarang tetap jalan. Penghapusannya dikerjakan Anggota 2 (M2).
        Schema::table('lapangan', function (Blueprint $table) {
            $table->unsignedInteger('id_jenis')->nullable();
            $table->foreign('id_jenis')->references('id_jenis')->on('jenis_lapangan')->restrictOnDelete();
        });

        DB::statement('UPDATE lapangan SET id_jenis = (SELECT id_jenis FROM jenis_lapangan WHERE lower(nama_jenis) = lapangan.jenis_lapangan)');
    }

    public function down(): void
    {
        Schema::table('lapangan', function (Blueprint $table) {
            $table->dropForeign(['id_jenis']);
            $table->dropColumn('id_jenis');
        });
        Schema::dropIfExists('jenis_lapangan');
    }
};

