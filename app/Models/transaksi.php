<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan', 'id_admin', 'tanggal_bayar', 'metode_pembayaran',
        'jumlah_bayar', 'bukti_bayar', 'tanggal_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bayar' => 'datetime',
            'tanggal_verifikasi' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_admin', 'id_pengguna');
    }
}
