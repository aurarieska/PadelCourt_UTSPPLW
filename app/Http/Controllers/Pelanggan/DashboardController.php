<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $id = Auth::id();

        $total = Pesanan::where('id_pengguna', $id)->count();
        $menungguBayar = Pesanan::where('id_pengguna', $id)->where('status_pesanan', Pesanan::STATUS_MENUNGGU_PEMBAYARAN)->count();
        $menungguVerifikasi = Pesanan::where('id_pengguna', $id)->where('status_pesanan', Pesanan::STATUS_MENUNGGU_VERIFIKASI)->count();

        $mendatang = DetailPesanan::with('lapangan')
            ->whereHas('pesanan', fn ($q) => $q->where('id_pengguna', $id)->where('status_pesanan', Pesanan::STATUS_TERVERIFIKASI))
            ->where('tanggal_main', '>=', today()->toDateString())
            ->orderBy('tanggal_main')->orderBy('jam_mulai')
            ->limit(5)->get();

        return view('pelanggan.dashboard', compact('total', 'menungguBayar', 'menungguVerifikasi', 'mendatang'));
    }
}
