<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->unsignedInteger('id_role')->nullable();
        });

        // Pindahkan data lama (kolom teks role) ke id_role
        DB::statement('UPDATE pengguna SET id_role = (SELECT id_role FROM roles WHERE roles.nama_role = pengguna.role)');
        DB::statement('ALTER TABLE pengguna ALTER COLUMN id_role SET NOT NULL');

        // CHECK constraint ikut terhapus bersama kolomnya
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->foreign('id_role')->references('id_role')->on('roles');
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropForeign(['id_role']);
            $table->string('role', 10)->default('pelanggan');
        });
        DB::statement('UPDATE pengguna SET role = (SELECT nama_role FROM roles WHERE roles.id_role = pengguna.id_role)');
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropColumn('id_role');
        });
    }
};
