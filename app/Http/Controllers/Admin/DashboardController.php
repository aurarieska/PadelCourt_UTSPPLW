<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use App\Models\Pesanan;
use App\Models\Transaksi;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Pendapatan = jumlah_bayar transaksi milik pesanan terverifikasi/selesai (subbab 6.5)
        $totalPendapatan = Transaksi::whereHas('pesanan', function ($q) {
            $q->whereIn('status_pesanan', [Pesanan::STATUS_TERVERIFIKASI, Pesanan::STATUS_SELESAI]);
        })->sum('jumlah_bayar');

        return view('admin.dashboard', [
            'totalPendapatan' => $totalPendapatan,
            'jumlahPesanan' => Pesanan::count(),
            'menungguVerifikasi' => Pesanan::where('status_pesanan', Pesanan::STATUS_MENUNGGU_VERIFIKASI)->count(),
            'lapanganAktif' => Lapangan::aktif()->count(),
            'pesananTerbaru' => Pesanan::orderByDesc('tanggal_pesan')->limit(5)->get(),
        ]);
    }
}
