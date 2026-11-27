<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\JenisLapanganRequest;
use App\Models\JenisLapangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenisLapanganController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari'));

        $jenis = JenisLapangan::withCount('lapangan')
            ->when($cari !== '', fn ($q) => $q->where('nama_jenis', 'ilike', "%{$cari}%"))
            ->orderBy('nama_jenis')
            ->paginate(10)
            ->withQueryString();

        return view('admin.jenis-lapangan.index', compact('jenis', 'cari'));
    }

    public function create(): View
    {
        return view('admin.jenis-lapangan.create');
    }

    public function store(JenisLapanganRequest $request): RedirectResponse
    {
        JenisLapangan::create($request->validated());

        return redirect()->route('admin.jenis-lapangan.index')->with('sukses', 'Jenis lapangan berhasil ditambahkan.');
    }

    public function edit(JenisLapangan $jenis): View
    {
        return view('admin.jenis-lapangan.edit', compact('jenis'));
    }

    public function update(JenisLapanganRequest $request, JenisLapangan $jenis): RedirectResponse
    {
        $jenis->update($request->validated());

        return redirect()->route('admin.jenis-lapangan.index')->with('sukses', 'Jenis lapangan berhasil diperbarui.');
    }

    public function destroy(JenisLapangan $jenis): RedirectResponse
    {
        // Jenis yang masih dipakai lapangan tidak boleh dihapus
        if ($jenis->lapangan()->exists()) {
            return back()->with('galat', "Jenis \"{$jenis->nama_jenis}\" masih dipakai lapangan, tidak dapat dihapus.");
        }

        $jenis->delete();

        return redirect()->route('admin.jenis-lapangan.index')->with('sukses', 'Jenis lapangan berhasil dihapus.');
    }
}
