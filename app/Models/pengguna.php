<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false;

    protected $fillable = ['nama_pengguna', 'email', 'password', 'no_telepon', 'role'];
    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['password' => 'hashed']; // otomatis di-hash saat disimpan (KNF-02)
    }

    // Skema tidak punya kolom remember_token
    public function getRememberTokenName(): string
    {
        return '';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_pengguna', 'id_pengguna');
    }
}
