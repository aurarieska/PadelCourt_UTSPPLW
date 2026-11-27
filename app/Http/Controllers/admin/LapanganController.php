<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LapanganRequest;
use App\Models\Lapangan;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LapanganController extends Controller
{
    // Daftar lapangan + pencarian nama (ILIKE = tidak case sensitive di PostgreSQL)
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));

        $lapangan = Lapangan::query()
            ->when($cari !== '', fn ($q) => $q->where('nama_lapangan', 'ilike', "%{$cari}%"))
            ->orderBy('id_lapangan')
            ->get();

        return view('admin.lapangan.index', compact('lapangan', 'cari'));
    }

    public function create(): View
    {
        return view('admin.lapangan.create');
    }

    public function store(LapanganRequest $request): RedirectResponse
    {
        Lapangan::create($this->siapkanData($request));

        return redirect()->route('admin.lapangan.index')
            ->with('sukses', 'Lapangan berhasil ditambahkan.');
    }

    public function edit(Lapangan $lapangan): View
    {
        return view('admin.lapangan.edit', compact('lapangan'));
    }

    public function update(LapanganRequest $request, Lapangan $lapangan): RedirectResponse
    {
        // Harga baru hanya berlaku untuk pemesanan berikutnya
        // Pesanan lama aman karena menyimpan harga_satuan sendiri (snapshot)
        $lapangan->update($this->siapkanData($request, $lapangan));

        return redirect()->route('admin.lapangan.index')
            ->with('sukses', 'Perubahan lapangan berhasil disimpan.');
    }

    public function destroy(Lapangan $lapangan): RedirectResponse
    {
        $pesanTolak = 'Lapangan "' . $lapangan->nama_lapangan . '" sudah memiliki riwayat pesanan '
            . 'atau ada di keranjang pelanggan sehingga tidak dapat dihapus. Nonaktifkan saja lapangan ini.';

        // Aturan RESTRICT: lapangan dengan riwayat pesanan tidak boleh dihapus
        if ($lapangan->detailPesanan()->exists() || $lapangan->keranjang()->exists()) {
            return redirect()->route('admin.lapangan.index')->with('galat', $pesanTolak);
        }

        try {
            $lapangan->delete();
        } catch (QueryException $e) {
            // Pengaman terakhir kalau FK menolak (misalnya ada pesanan yang masuk bersamaan)
            return redirect()->route('admin.lapangan.index')->with('galat', $pesanTolak);
        }

        if ($lapangan->foto) {
            Storage::disk('public')->delete('lapangan/' . $lapangan->foto);
        }

        return redirect()->route('admin.lapangan.index')
            ->with('sukses', 'Lapangan berhasil dihapus.');
    }

    /**
     * Susun data dari form. Toggle "aktif" diubah menjadi 'aktif'/'nonaktif'
     * Foto disimpan di dalam storage/app/public/lapangan, kolom foto isinya hanya nama file
     */
    private function siapkanData(LapanganRequest $request, ?Lapangan $lapangan = null): array
    {
        $data = [
            'nama_lapangan' => $request->input('nama_lapangan'),
            'jenis_lapangan' => $request->input('jenis_lapangan'),
            'harga_per_sesi' => (int) $request->input('harga_per_sesi'),
            'deskripsi' => $request->input('deskripsi'),
            'status_lapangan' => $request->boolean('aktif') ? 'aktif' : 'nonaktif',
        ];

        if ($request->hasFile('foto')) {
            // Hapus foto lama saat diganti
            if ($lapangan?->foto) {
                Storage::disk('public')->delete('lapangan/' . $lapangan->foto);
            }

            $data['foto'] = basename($request->file('foto')->store('lapangan', 'public'));
        }

        return $data;
    }
}
