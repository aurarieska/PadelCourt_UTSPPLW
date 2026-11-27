<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lapangan extends Model
{
    protected $table = 'lapangan';
    protected $primaryKey = 'id_lapangan';
    public $timestamps = false;

    protected $fillable = [
        'nama_lapangan',
        'id_jenis',
        'harga_per_sesi',
        'deskripsi',
        'foto',
        'status_lapangan',
    ];

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_lapangan', 'aktif');
    }

    // Pemakaian di view: $lapangan->foto_url (null kalau fotonya belum ada)
    protected function fotoUrl(): Attribute
    {
        return Attribute::get(
            fn() => $this->foto ? asset('storage/lapangan/' . $this->foto) : null
        );
    }

    // Kompatibilitas: view lama yang membaca $lapangan->jenis_lapangan tetap mendapat 'indoor' / 'outdoor' dst.
    protected function jenisLapangan(): Attribute
    {
        return Attribute::get(fn() => $this->jenis ? strtolower($this->jenis->nama_jenis) : null);
    }

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(JenisLapangan::class, 'id_jenis', 'id_jenis');
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
