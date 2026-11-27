<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false;

    protected $fillable = ['nama_pengguna', 'email', 'password', 'no_telepon', 'id_role'];
    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['password' => 'hashed']; // otomatis di-hash saat disimpan
    }

    // Skema tidak punya kolom remember_token
    public function getRememberTokenName(): string
    {
        return '';
    }

    public function peran(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    // Kompatibilitas: kode lama yang membaca $user->role tetap mendapat teks 'admin' / 'pelanggan'
    protected function role(): Attribute
    {
        return Attribute::get(fn () => $this->peran?->nama_role);
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
