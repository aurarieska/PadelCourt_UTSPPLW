<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengaman: lapangan tanpa jenis diberi jenis pertama, lalu id_jenis diwajibkan
        DB::statement('UPDATE lapangan SET id_jenis = (SELECT MIN(id_jenis) FROM jenis_lapangan) WHERE id_jenis IS NULL');
        DB::statement('ALTER TABLE lapangan ALTER COLUMN id_jenis SET NOT NULL');

        // CHECK constraint chk_lapangan_jenis ikut terhapus bersama kolomnya
        Schema::table('lapangan', function (Blueprint $table) {
            $table->dropColumn('jenis_lapangan');
        });
    }

    public function down(): void
    {
        Schema::table('lapangan', function (Blueprint $table) {
            $table->string('jenis_lapangan', 10)->default('indoor');
        });
        DB::statement("UPDATE lapangan SET jenis_lapangan = lower((SELECT nama_jenis FROM jenis_lapangan WHERE jenis_lapangan.id_jenis = lapangan.id_jenis)) WHERE (SELECT lower(nama_jenis) FROM jenis_lapangan WHERE jenis_lapangan.id_jenis = lapangan.id_jenis) IN ('indoor','outdoor')");
        DB::statement('ALTER TABLE lapangan ALTER COLUMN id_jenis DROP NOT NULL');
    }
};
