<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\JenisLapangan;
use App\Models\Keranjang;
use App\Models\Lapangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// Katalog publik: dapat dibuka Guest, baca saja. 
class KatalogController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari'));
        $idJenis = $request->query('jenis');

        $lapangan = Lapangan::aktif()->with('jenis')
            ->when($cari !== '', fn($q) => $q->where('nama_lapangan', 'ilike', "%{$cari}%"))
            ->when($idJenis, fn($q) => $q->where('id_jenis', $idJenis))
            ->orderBy('nama_lapangan')
            ->paginate(9)
            ->withQueryString();

        $daftarJenis = JenisLapangan::orderBy('nama_jenis')->get();

        return view('katalog.index', compact('lapangan', 'daftarJenis', 'cari', 'idJenis'));
    }

    public function show(Request $request, Lapangan $lapangan): View
    {
        abort_unless($lapangan->status_lapangan === 'aktif', 404);

        $tanggal = rescue(fn() => Carbon::parse($request->query('tanggal')), today(), false);
        if ($tanggal->lt(today())) {
            $tanggal = today();
        }
        $tgl = $tanggal->toDateString();

        $terisi = DetailPesanan::where('id_lapangan', $lapangan->id_lapangan)
            ->where('tanggal_main', $tgl)->where('aktif', true)
            ->pluck('jam_mulai')->map(fn($j) => substr($j, 0, 5))->all();

        $dikeranjang = Auth::check()
            ? Keranjang::where('id_pengguna', Auth::id())->where('id_lapangan', $lapangan->id_lapangan)
            ->where('tanggal_main', $tgl)->get()
            ->mapWithKeys(fn($k) => [substr($k->jam_mulai, 0, 5) => $k->id_keranjang])->all()
            : [];

        $lapangan->load('jenis');
        $jamSesi = DetailPesanan::jamSesi();

        return view('katalog.show', compact('lapangan', 'tgl', 'terisi', 'dikeranjang', 'jamSesi'));
    }
}
