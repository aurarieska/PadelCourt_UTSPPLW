<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id_pesanan');
            $table->string('kode_pesanan', 10)->unique();
            $table->unsignedInteger('id_pengguna')->nullable(); // kosong untuk walk-in
            $table->string('nama_pemesan', 150);
            $table->string('no_telepon_pemesan', 15);
            $table->string('jenis_pesanan', 10);
            $table->timestamp('tanggal_pesan')->useCurrent();
            $table->timestamp('batas_bayar')->nullable(); // kosong untuk walk-in
            $table->integer('total_harga');
            $table->string('status_pesanan', 20)->default('menunggu pembayaran');
            $table->string('alasan_batal', 20)->nullable();
            $table->text('catatan_batal')->nullable();

            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna');
        });

        DB::statement("ALTER TABLE pesanan ADD CONSTRAINT chk_pesanan_jenis CHECK (jenis_pesanan IN ('daring', 'walk-in'))");
        DB::statement("ALTER TABLE pesanan ADD CONSTRAINT chk_pesanan_status CHECK (status_pesanan IN ('menunggu pembayaran', 'menunggu verifikasi', 'terverifikasi', 'selesai', 'batal'))");
        DB::statement("ALTER TABLE pesanan ADD CONSTRAINT chk_pesanan_alasan CHECK (alasan_batal IS NULL OR alasan_batal IN ('pelanggan', 'ditolak', 'kedaluwarsa'))");
        // pesanan daring wajib punya pengguna; walk-in boleh tidak
        DB::statement("ALTER TABLE pesanan ADD CONSTRAINT chk_pesanan_daring CHECK (jenis_pesanan <> 'daring' OR id_pengguna IS NOT NULL)");
        // penolakan admin wajib disertai catatan
        DB::statement("ALTER TABLE pesanan ADD CONSTRAINT chk_pesanan_ditolak CHECK (alasan_batal IS DISTINCT FROM 'ditolak' OR (catatan_batal IS NOT NULL AND length(trim(catatan_batal)) > 0))");
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
