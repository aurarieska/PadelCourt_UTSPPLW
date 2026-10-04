<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LapanganController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\WalkInController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PelangganController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'beranda'])->name('beranda');

// Pengguna yang belum login hanya bisa membuka halaman login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'tampilLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Area admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD lapangan
    Route::resource('lapangan', LapanganController::class)
        ->except(['show'])
        ->parameters(['lapangan' => 'lapangan']);

    // Pesanan walk-in
    Route::get('/walk-in', [WalkInController::class, 'create'])->name('walkin.create');
    Route::get('/walk-in/ketersediaan', [WalkInController::class, 'ketersediaan'])->name('walkin.ketersediaan');
    Route::post('/walk-in', [WalkInController::class, 'store'])->name('walkin.store');

    // Semua pesanan
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::patch('/pesanan/{pesanan}/selesai', [PesananController::class, 'selesai'])->name('pesanan.selesai');
});

// Area pelanggan 
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/lapangan', [PelangganController::class, 'lapangan'])->name('pelanggan.lapangan');
});
