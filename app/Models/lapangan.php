<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    // Pemakaian di view: $lapangan->foto_url (null kalau fotonya belum ada)
    protected function fotoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->foto ? asset('storage/lapangan/' . $this->foto) : null
        );
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_lapangan', 'id_lapangan');
    }

    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_lapangan', 'id_lapangan');
    }
}
