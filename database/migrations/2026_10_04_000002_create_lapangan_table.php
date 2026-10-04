<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lapangan', function (Blueprint $table) {
            $table->increments('id_lapangan');
            $table->string('nama_lapangan', 100);
            $table->string('jenis_lapangan', 10);
            $table->integer('harga_per_sesi');
            $table->text('deskripsi')->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('status_lapangan', 10)->default('aktif');
        });

        DB::statement("ALTER TABLE lapangan ADD CONSTRAINT chk_lapangan_jenis CHECK (jenis_lapangan IN ('indoor', 'outdoor'))");
        DB::statement("ALTER TABLE lapangan ADD CONSTRAINT chk_lapangan_status CHECK (status_lapangan IN ('aktif', 'nonaktif'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('lapangan');
    }
};
