<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PesananController extends Controller
{
    // Kunci tab di URL = status di database 
    private const TAB = [
        'semua' => null,
        'menunggu-verifikasi' => Pesanan::STATUS_MENUNGGU_VERIFIKASI,
        'terverifikasi' => Pesanan::STATUS_TERVERIFIKASI,
        'selesai' => Pesanan::STATUS_SELESAI,
        'batal' => Pesanan::STATUS_BATAL,
    ];

    public function index(Request $request): View
    {
        $tab = $request->query('status', 'semua');
        if (! array_key_exists($tab, self::TAB)) {
            $tab = 'semua';
        }

        // Tanggal yang formatnya tidak valid diabaikan 
        $tanggal = $request->query('tanggal');
        if ($tanggal && Validator::make(['t' => $tanggal], ['t' => 'date_format:Y-m-d'])->fails()) {
            $tanggal = null;
        }

        $pesanan = Pesanan::query()
            ->when(self::TAB[$tab], fn($q, $status) => $q->where('status_pesanan', $status))
            ->when($tanggal, fn($q, $t) => $q->whereDate('tanggal_pesan', $t))
            ->orderByDesc('tanggal_pesan')
            ->orderByDesc('id_pesanan')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pesanan.index', compact('pesanan', 'tab', 'tanggal'));
    }

    public function show(Pesanan $pesanan): View
    {
        $pesanan->load(['detail.lapangan', 'transaksi.admin']);

        return view('admin.pesanan.show', compact('pesanan'));
    }

    // Hanya pesanan terverifikasi, dan hanya setelah seluruh waktu bermain berakhir
    public function selesai(Pesanan $pesanan): RedirectResponse
    {
        $pesanan->load('detail');

        if ($pesanan->status_pesanan !== Pesanan::STATUS_TERVERIFIKASI) {
            return back()->with('galat', 'Hanya pesanan berstatus terverifikasi yang dapat ditandai selesai.');
        }

        if (! $pesanan->sudahBerakhir()) {
            return back()->with('galat', 'Pesanan baru dapat ditandai selesai setelah waktu bermain seluruh sesi berakhir.');
        }

        $pesanan->update(['status_pesanan' => Pesanan::STATUS_SELESAI]);

        return redirect()->route('admin.pesanan.show', $pesanan)
            ->with('sukses', "Pesanan {$pesanan->kode_pesanan} telah ditandai selesai.");
    }
}
