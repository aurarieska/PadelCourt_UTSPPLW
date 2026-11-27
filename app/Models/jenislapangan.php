<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisLapangan extends Model
{
    protected $table = 'jenis_lapangan';
    protected $primaryKey = 'id_jenis';
    public $timestamps = false;

    protected $fillable = ['nama_jenis', 'keterangan'];

    public function lapangan(): HasMany
    {
        return $this->hasMany(Lapangan::class, 'id_jenis', 'id_jenis');
    }
}
