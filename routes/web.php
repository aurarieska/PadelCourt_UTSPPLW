<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LapanganController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PelangganController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'beranda'])->name('beranda');

// Pengguna yang belum login cuman bisa membuka halaman login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'tampilLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Area admin (tahap 3 menambah route walk-in dan pesanan di grup ini)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD lapangan: admin.lapangan.index/create/store/edit/update/destroy
    Route::resource('lapangan', LapanganController::class)
        ->except(['show'])
        ->parameters(['lapangan' => 'lapangan']);
});

// Area pelanggan (placeholder)
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/lapangan', [PelangganController::class, 'lapangan'])->name('pelanggan.lapangan');
});
