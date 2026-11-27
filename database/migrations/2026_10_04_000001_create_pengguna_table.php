<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->increments('id_pengguna');
            $table->string('nama_pengguna', 150);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('no_telepon', 15);
            $table->string('role', 10)->default('pelanggan');
        });

        DB::statement("ALTER TABLE pengguna ADD CONSTRAINT chk_pengguna_role CHECK (role IN ('pelanggan', 'admin'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
