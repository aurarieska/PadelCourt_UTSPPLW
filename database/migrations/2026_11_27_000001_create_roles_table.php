<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id_role');
            $table->string('nama_role', 20)->unique();
        });

        // Urutan penting: Role 1 = admin, Role 2 = pelanggan
        DB::table('roles')->insert([
            ['nama_role' => 'admin'],
            ['nama_role' => 'pelanggan'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
