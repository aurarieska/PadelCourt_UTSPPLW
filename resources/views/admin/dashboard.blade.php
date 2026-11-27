@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-3xl font-extrabold text-slate-900">Dashboard</h1>
    <p class="mt-1 text-sm text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>

    {{-- Empat kartu ringkasan (KF-25) --}}
    <div class="mt-6 grid grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Total Pendapatan</p>
            <p class="mt-2 text-2xl font-extrabold text-teal-800">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Jumlah Pesanan</p>
            <p class="mt-2 text-2xl font-extrabold">{{ $jumlahPesanan }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Menunggu Verifikasi</p>
            <p class="mt-2 text-2xl font-extrabold">{{ $menungguVerifikasi }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Lapangan Aktif</p>
            <p class="mt-2 text-2xl font-extrabold">{{ $lapanganAktif }}</p>
        </div>
    </div>

    {{-- Pesanan terbaru --}}
    <div class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="font-bold">Pesanan Terbaru</h2>

        <table class="mt-4 w-full text-left text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <th class="pb-3">Kode Pesanan</th>
                    <th class="pb-3">Customer</th>
                    <th class="pb-3">Total</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pesananTerbaru as $p)
                    <tr>
                        <td class="py-4 font-bold text-teal-800">{{ $p->kode_pesanan }}</td>
                        <td class="py-4">{{ $p->nama_pemesan }}</td>
                        <td class="py-4 font-semibold">Rp{{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td class="py-4">@include('partials.status-badge', ['status' => $p->status_pesanan])</td>
                        <td class="py-4 text-right">
                            {{-- Link detail diaktifkan di tahap 3 --}}
                            <a href="{{ Route::has('admin.pesanan.show') ? route('admin.pesanan.show', $p->id_pesanan) : '#' }}"
                               class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold hover:bg-slate-200">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400">Belum ada pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
