
<?php

use App\Http\Controllers\Admin\JenisLapanganController;
use App\Http\Controllers\Admin\VerifikasiController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LapanganController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\WalkInController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\Pelanggan\DashboardController as PelangganDashboardController;
use App\Http\Controllers\Pelanggan\KeranjangController;
use App\Http\Controllers\Pelanggan\PemesananController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check() && auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('katalog.index');
})->name('beranda');

// Pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'tampilLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
    Route::get('/register', [RegistrasiController::class, 'tampil'])->name('register');
    Route::post('/register', [RegistrasiController::class, 'simpan'])->name('register.proses');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Katalog
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{lapangan}', [KatalogController::class, 'show'])->name('katalog.show');

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

    // M1 Jenis Lapangan
    Route::resource('jenis-lapangan', JenisLapanganController::class)
        ->except(['show'])
        ->parameters(['jenis-lapangan' => 'jenis']);

    // T2 Verifikasi pembayaran
    Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::patch('/verifikasi/{pesanan}/setujui', [VerifikasiController::class, 'setujui'])->name('verifikasi.setujui');
    Route::patch('/verifikasi/{pesanan}/tolak', [VerifikasiController::class, 'tolak'])->name('verifikasi.tolak');
});

// Area pelanggan
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/lapangan', [PelangganController::class, 'lapangan'])->name('pelanggan.lapangan');
});

Route::middleware(['auth', 'role:pelanggan'])->name('pelanggan.')->group(function () {
    Route::get('/akun', [PelangganDashboardController::class, 'index'])->name('dashboard');

    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::delete('/keranjang/{keranjang}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

    Route::post('/checkout', [PemesananController::class, 'checkout'])->name('checkout');
    Route::get('/pesanan-saya', [PemesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan-saya/{pesanan}', [PemesananController::class, 'show'])->name('pesanan.show');
    Route::post('/pesanan-saya/{pesanan}/bayar', [PemesananController::class, 'bayar'])->name('pesanan.bayar');
    Route::patch('/pesanan-saya/{pesanan}/batal', [PemesananController::class, 'batal'])->name('pesanan.batal');
});
