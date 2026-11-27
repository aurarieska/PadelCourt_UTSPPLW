<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * T2 - Verifikasi pembayaran (alur status dua role:
 * pelanggan mengunggah bukti -> admin menyetujui / menolak).
 */
class VerifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari'));

        $pesanan = Pesanan::with(['detail.lapangan', 'transaksi'])
            ->where('status_pesanan', Pesanan::STATUS_MENUNGGU_VERIFIKASI)
            ->when($cari !== '', fn ($q) => $q->where(function ($w) use ($cari) {
                $w->where('kode_pesanan', 'ilike', "%{$cari}%")
                  ->orWhere('nama_pemesan', 'ilike', "%{$cari}%");
            }))
            ->orderBy('tanggal_pesan')
            ->paginate(10)
            ->withQueryString();

        return view('admin.verifikasi.index', compact('pesanan', 'cari'));
    }

    public function setujui(Pesanan $pesanan): RedirectResponse
    {
        $hasil = DB::transaction(function () use ($pesanan) {
            $p = Pesanan::lockForUpdate()->findOrFail($pesanan->id_pesanan);

            if ($p->status_pesanan !== Pesanan::STATUS_MENUNGGU_VERIFIKASI) {
                return false; // sudah diproses (misalnya oleh admin lain)
            }

            $p->update(['status_pesanan' => Pesanan::STATUS_TERVERIFIKASI]);
            $p->transaksi()->update([
                'id_admin' => Auth::id(),
                'tanggal_verifikasi' => now(),
            ]);

            return true;
        });

        return $hasil
            ? back()->with('sukses', "Pesanan {$pesanan->kode_pesanan} diverifikasi.")
            : back()->with('galat', 'Pesanan ini sudah diproses sebelumnya.');
    }

    public function tolak(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $data = $request->validate(
            ['catatan_batal' => 'required|string|min:5|max:255'],
            ['catatan_batal.required' => 'Alasan penolakan wajib diisi.', 'catatan_batal.min' => 'Alasan minimal 5 karakter.']
        );

        $hasil = DB::transaction(function () use ($pesanan, $data) {
            $p = Pesanan::lockForUpdate()->findOrFail($pesanan->id_pesanan);

            if ($p->status_pesanan !== Pesanan::STATUS_MENUNGGU_VERIFIKASI) {
                return false;
            }

            $p->update([
                'status_pesanan' => Pesanan::STATUS_BATAL,
                'alasan_batal' => 'ditolak',
                'catatan_batal' => $data['catatan_batal'],
            ]);
            $p->detail()->update(['aktif' => false]); // jadwal dibuka kembali
            $p->transaksi()->update([
                'id_admin' => Auth::id(),
                'tanggal_verifikasi' => now(),
            ]);

            return true;
        });

        return $hasil
            ? back()->with('sukses', "Pesanan {$pesanan->kode_pesanan} ditolak.")
            : back()->with('galat', 'Pesanan ini sudah diproses sebelumnya.');
    }
}
