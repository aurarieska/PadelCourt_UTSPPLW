<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lapangan extends Model
{
    protected $table = 'lapangan';
    protected $primaryKey = 'id_lapangan';
    public $timestamps = false;

    protected $fillable = [
        'nama_lapangan', 'jenis_lapangan', 'harga_per_sesi',
        'deskripsi', 'foto', 'status_lapangan',
    ];

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_lapangan', 'aktif');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_lapangan', 'id_lapangan');
    }
}
