<?php

use App\Http\Controllers\Admin\DashboardController;
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

// Area admin (tahap 2 dan 3 menambah route di grup ini)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Area pelanggan (placeholder)
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/lapangan', [PelangganController::class, 'lapangan'])->name('pelanggan.lapangan');
});
