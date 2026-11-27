<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Keranjang;
use App\Models\Lapangan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KeranjangController extends Controller
{
    public function index(): View
    {
        $item = Keranjang::with('lapangan')->where('id_pengguna', Auth::id())
            ->orderBy('tanggal_main')->orderBy('jam_mulai')->get();

        return view('keranjang.index', ['item' => $item, 'total' => $item->sum('harga_satuan')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_lapangan' => 'required|exists:lapangan,id_lapangan',
            'tanggal_main' => 'required|date|after_or_equal:today',
            'jam_mulai' => ['required', Rule::in(DetailPesanan::jamSesi())],
        ]);

        $lapangan = Lapangan::aktif()->find($data['id_lapangan']);
        if (! $lapangan) {
            return back()->with('galat', 'Lapangan tidak tersedia.');
        }

        if (Carbon::parse($data['tanggal_main'] . ' ' . $data['jam_mulai'])->isPast()) {
            return back()->with('galat', 'Sesi tersebut sudah lewat.');
        }

        $sudahDipesan = DetailPesanan::where('id_lapangan', $data['id_lapangan'])
            ->where('tanggal_main', $data['tanggal_main'])->where('jam_mulai', $data['jam_mulai'])
            ->where('aktif', true)->exists();
        if ($sudahDipesan) {
            return back()->with('galat', 'Sesi tersebut sudah dipesan orang lain.');
        }

        $sudahAda = Keranjang::where('id_pengguna', Auth::id())->where('id_lapangan', $data['id_lapangan'])
            ->where('tanggal_main', $data['tanggal_main'])->where('jam_mulai', $data['jam_mulai'])->exists();
        if ($sudahAda) {
            return back()->with('galat', 'Sesi tersebut sudah ada di keranjang.');
        }

        Keranjang::create($data + [
            'id_pengguna' => Auth::id(),
            'harga_satuan' => $lapangan->harga_per_sesi, // snapshot harga
        ]);

        return back()->with('sukses', 'Sesi ditambahkan ke keranjang.');
    }

    public function destroy(Keranjang $keranjang): RedirectResponse
    {
        abort_unless((int) $keranjang->id_pengguna === (int) Auth::id(), 403);
        $keranjang->delete();

        return back()->with('sukses', 'Sesi dihapus dari keranjang.');
    }
}
