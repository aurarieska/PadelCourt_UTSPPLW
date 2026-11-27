<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalkInRequest;
use App\Models\DetailPesanan;
use App\Models\Lapangan;
use App\Models\Pesanan;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WalkInController extends Controller
{
    public function create(): View
    {
        return view('admin.walkin.create', [
            'lapangan' => Lapangan::aktif()->orderBy('id_lapangan')
                ->get(['id_lapangan', 'nama_lapangan', 'harga_per_sesi']),
            'jam' => DetailPesanan::jamSesi(),
            'hariIni' => now()->toDateString(),
        ]);
    }

    // jam mana saja yang sudah terisi pada lapangan + tanggal
    public function ketersediaan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_lapangan' => ['required', 'integer', 'exists:lapangan,id_lapangan'],
            'tanggal' => ['required', 'date_format:Y-m-d'],
        ]);

        $terisi = DetailPesanan::where('aktif', true)
            ->where('id_lapangan', $data['id_lapangan'])
            ->where('tanggal_main', $data['tanggal'])
            ->pluck('jam_mulai')
            ->map(fn($jam) => substr($jam, 0, 5))
            ->values();

        return response()->json(['terisi' => $terisi]);
    }

    public function store(WalkInRequest $request): RedirectResponse
    {
        // Susun item: sesi kembar lapangan+tanggal+jam sama digabung, lalu urut waktu
        $items = collect($request->validated('items'))
            ->map(fn($i) => [
                'id_lapangan' => (int) $i['id_lapangan'],
                'tanggal' => $i['tanggal'],
                'jam_mulai' => $i['jam_mulai'] . ':00',
            ])
            ->unique(fn($i) => $i['id_lapangan'] . '|' . $i['tanggal'] . '|' . $i['jam_mulai'])
            ->sortBy(fn($i) => $i['tanggal'] . ' ' . $i['jam_mulai'])
            ->values();

        try {
            // Semua langkah di bawah berhasil bersama atau dibatalkan bersama 
            $pesanan = DB::transaction(function () use ($request, $items) {
                $lapangan = Lapangan::aktif()
                    ->whereIn('id_lapangan', $items->pluck('id_lapangan')->unique())
                    ->get()
                    ->keyBy('id_lapangan');

                // 1. Semua lapangan harus aktif
                foreach ($items as $i) {
                    if (! $lapangan->has($i['id_lapangan'])) {
                        throw ValidationException::withMessages([
                            'items' => 'Ada lapangan yang sedang nonaktif. Muat ulang halaman lalu pilih lapangan lain.',
                        ]);
                    }
                }

                // 2. Cek ulang jadwal bentrok 
                foreach ($items as $i) {
                    $bentrok = DetailPesanan::where('aktif', true)
                        ->where('id_lapangan', $i['id_lapangan'])
                        ->where('tanggal_main', $i['tanggal'])
                        ->where('jam_mulai', $i['jam_mulai'])
                        ->exists();

                    if ($bentrok) {
                        throw ValidationException::withMessages([
                            'items' => sprintf(
                                'Sesi %s pada %s pukul %s sudah terisi. Hapus dari daftar lalu pilih sesi lain.',
                                $lapangan[$i['id_lapangan']]->nama_lapangan,
                                Carbon::parse($i['tanggal'])->translatedFormat('d M Y'),
                                substr($i['jam_mulai'], 0, 5)
                            ),
                        ]);
                    }
                }

                // 3. Harga selalu dari database
                $total = $items->sum(fn($i) => $lapangan[$i['id_lapangan']]->harga_per_sesi);

                // 4. Pesanan walk-in: tanpa pengguna, tanpa batas bayar, langsung terverifikasi
                $pesanan = Pesanan::create([
                    'kode_pesanan' => Str::random(10), // sementara, diganti tepat di bawah
                    'id_pengguna' => null,
                    'nama_pemesan' => $request->input('nama_pemesan'),
                    'no_telepon_pemesan' => $request->input('no_telepon_pemesan'),
                    'jenis_pesanan' => 'walk-in',
                    'tanggal_pesan' => now(),
                    'batas_bayar' => null,
                    'total_harga' => $total,
                    'status_pesanan' => Pesanan::STATUS_TERVERIFIKASI,
                ]);

                // Kode unik per checkout: CRT-0001, CRT-0002 (berdasarkan id_pesanan)
                $pesanan->update([
                    'kode_pesanan' => 'CRT-' . str_pad($pesanan->id_pesanan, 4, '0', STR_PAD_LEFT),
                ]);

                // 5. Detail per sesi (1 jam) dengan harga satuan saat ini
                foreach ($items as $i) {
                    DetailPesanan::create([
                        'id_pesanan' => $pesanan->id_pesanan,
                        'id_lapangan' => $i['id_lapangan'],
                        'tanggal_main' => $i['tanggal'],
                        'jam_mulai' => $i['jam_mulai'],
                        'harga_satuan' => $lapangan[$i['id_lapangan']]->harga_per_sesi,
                        'aktif' => true,
                    ]);
                }

                // 6. Transaksi lunas: tunai atau QRIS di tempat, tanpa bukti unggahan
                Transaksi::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_admin' => Auth::id(),
                    'tanggal_bayar' => now(),
                    'metode_pembayaran' => $request->input('metode_pembayaran'),
                    'jumlah_bayar' => $total,
                    'bukti_bayar' => null,
                    'tanggal_verifikasi' => now(),
                ]);

                return $pesanan;
            });
        } catch (QueryException $e) {
            // 23505 = unique violation: unique index uq_jadwal_aktif menolak sesi ganda
            if (($e->errorInfo[0] ?? null) === '23505') {
                throw ValidationException::withMessages([
                    'items' => 'Salah satu sesi baru saja dipesan. Ganti tanggal atau lapangan untuk memuat ketersediaan terbaru, lalu pilih sesi lain.',
                ]);
            }

            throw $e;
        }

        return redirect()->route('admin.pesanan.show', $pesanan)
            ->with('sukses', "Pesanan walk-in {$pesanan->kode_pesanan} berhasil dicatat dan ditandai lunas.");
    }
}
