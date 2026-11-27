<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * T1 - Pemesanan daring (header-detail, disimpan dengan DB::transaction()).
 * Alur: keranjang -> checkout -> unggah bukti -> (admin memverifikasi di T2).
 */
class PemesananController extends Controller
{
    public function checkout(): RedirectResponse
    {
        $user = Auth::user();
        $item = Keranjang::where('id_pengguna', $user->id_pengguna)->get();

        if ($item->isEmpty()) {
            return back()->with('galat', 'Keranjang masih kosong.');
        }

        foreach ($item as $i) {
            if (Carbon::parse($i->tanggal_main . ' ' . $i->jam_mulai)->isPast()) {
                return back()->with('galat', 'Ada sesi di keranjang yang sudah lewat. Hapus sesi tersebut.');
            }
        }

        try {
            $pesanan = DB::transaction(function () use ($user, $item) {
                $pesanan = Pesanan::create([
                    'kode_pesanan' => $this->kodeBaru(),
                    'id_pengguna' => $user->id_pengguna,
                    'nama_pemesan' => $user->nama_pengguna,
                    'no_telepon_pemesan' => $user->no_telepon,
                    'jenis_pesanan' => 'daring',
                    'tanggal_pesan' => now(),
                    'batas_bayar' => now()->addMinutes(30),
                    'total_harga' => $item->sum('harga_satuan'),
                    'status_pesanan' => Pesanan::STATUS_MENUNGGU_PEMBAYARAN,
                ]);

                foreach ($item as $i) {
                    $pesanan->detail()->create([
                        'id_lapangan' => $i->id_lapangan,
                        'tanggal_main' => $i->tanggal_main,
                        'jam_mulai' => $i->jam_mulai,
                        'harga_satuan' => $i->harga_satuan,
                        'aktif' => true,
                    ]);
                }

                Keranjang::where('id_pengguna', $user->id_pengguna)->delete();

                return $pesanan;
            });
        } catch (UniqueConstraintViolationException) {
            // Index uq_jadwal_aktif menolak sesi yang baru saja diambil orang lain
            return back()->with('galat', 'Ada sesi yang baru saja dipesan orang lain. Hapus sesi itu lalu coba lagi.');
        }

        return redirect()->route('pelanggan.pesanan.show', $pesanan)
            ->with('sukses', 'Pesanan dibuat. Selesaikan pembayaran dalam 30 menit.');
    }

    public function index(): View
    {
        $pesanan = Pesanan::withCount('detail')->where('id_pengguna', Auth::id())
            ->orderByDesc('tanggal_pesan')->paginate(10);

        return view('pesanan.index', compact('pesanan'));
    }

    public function show(Pesanan $pesanan): View
    {
        $this->pastikanMilikSaya($pesanan);
        $pesanan->load(['detail.lapangan', 'transaksi']);

        return view('pesanan.show', compact('pesanan'));
    }

    public function bayar(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $this->pastikanMilikSaya($pesanan);

        $request->validate(
            ['bukti' => 'required|image|mimes:jpg,jpeg,png|max:5120'],
            ['bukti.required' => 'Bukti pembayaran wajib diunggah.', 'bukti.image' => 'Berkas harus berupa gambar.',
             'bukti.mimes' => 'Format harus JPG atau PNG.', 'bukti.max' => 'Ukuran maksimal 5 MB.']
        );

        $nama = $request->file('bukti')->hashName();

        $pesan = DB::transaction(function () use ($pesanan, $request, $nama) {
            $p = Pesanan::lockForUpdate()->findOrFail($pesanan->id_pesanan);

            if ($p->status_pesanan !== Pesanan::STATUS_MENUNGGU_PEMBAYARAN) {
                return 'Pesanan ini tidak dapat dibayar lagi.';
            }
            if ($p->batas_bayar && $p->batas_bayar->isPast()) {
                return 'Batas waktu pembayaran sudah lewat.';
            }

            $request->file('bukti')->storeAs('bukti', $nama, 'public');

            Transaksi::create([
                'id_pesanan' => $p->id_pesanan,
                'tanggal_bayar' => now(),
                'metode_pembayaran' => 'QRIS',
                'jumlah_bayar' => $p->total_harga,
                'bukti_bayar' => $nama,
            ]);
            $p->update(['status_pesanan' => Pesanan::STATUS_MENUNGGU_VERIFIKASI]);

            return null;
        });

        return $pesan
            ? back()->with('galat', $pesan)
            : back()->with('sukses', 'Bukti terkirim. Menunggu verifikasi admin.');
    }

    public function batal(Pesanan $pesanan): RedirectResponse
    {
        $this->pastikanMilikSaya($pesanan);

        $berhasil = DB::transaction(function () use ($pesanan) {
            $p = Pesanan::lockForUpdate()->findOrFail($pesanan->id_pesanan);

            if ($p->status_pesanan !== Pesanan::STATUS_MENUNGGU_PEMBAYARAN) {
                return false;
            }

            $p->update(['status_pesanan' => Pesanan::STATUS_BATAL, 'alasan_batal' => 'pelanggan']);
            $p->detail()->update(['aktif' => false]); // jadwal dibuka kembali

            return true;
        });

        return $berhasil
            ? back()->with('sukses', 'Pesanan dibatalkan.')
            : back()->with('galat', 'Pesanan ini tidak dapat dibatalkan.');
    }

    private function pastikanMilikSaya(Pesanan $pesanan): void
    {
        abort_unless((int) $pesanan->id_pengguna === (int) Auth::id(), 403);
    }

    private function kodeBaru(): string
    {
        do {
            $kode = 'PC' . strtoupper(Str::random(8));
        } while (Pesanan::where('kode_pesanan', $kode)->exists());

        return $kode;
    }
}
