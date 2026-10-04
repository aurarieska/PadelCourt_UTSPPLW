<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    public const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu pembayaran';
    public const STATUS_MENUNGGU_VERIFIKASI = 'menunggu verifikasi';
    public const STATUS_TERVERIFIKASI = 'terverifikasi';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_BATAL = 'batal';

    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public $timestamps = false;

    protected $fillable = [
        'kode_pesanan', 'id_pengguna', 'nama_pemesan', 'no_telepon_pemesan',
        'jenis_pesanan', 'tanggal_pesan', 'batas_bayar', 'total_harga',
        'status_pesanan', 'alasan_batal', 'catatan_batal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pesan' => 'datetime',
            'batas_bayar' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function transaksi(): HasOne
    {
        return $this->hasOne(Transaksi::class, 'id_pesanan', 'id_pesanan');
    }
}
