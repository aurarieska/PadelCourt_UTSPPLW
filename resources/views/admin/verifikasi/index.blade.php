@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<h1 class="mb-6 text-2xl font-extrabold">Verifikasi Pembayaran</h1>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari kode atau nama pemesan..."
           class="w-72 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-teal-600">
    <button class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white">Cari</button>
</form>

@error('catatan_batal')
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-600">{{ $message }}</div>
@enderror

<div class="overflow-hidden rounded-2xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-100 text-xs uppercase text-slate-500">
            <tr>
                <th class="px-5 py-3">Kode</th><th class="px-5 py-3">Pemesan</th><th class="px-5 py-3">Sesi</th>
                <th class="px-5 py-3">Total</th><th class="px-5 py-3">Bukti</th><th class="px-5 py-3">Tindakan</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($pesanan as $p)
            <tr class="border-t border-slate-100 align-top">
                <td class="px-5 py-3 font-semibold">
                    <a href="{{ route('admin.pesanan.show', $p) }}" class="text-teal-700 hover:underline">{{ $p->kode_pesanan }}</a>
                </td>
                <td class="px-5 py-3">{{ $p->nama_pemesan }}<br><span class="text-xs text-slate-500">{{ $p->no_telepon_pemesan }}</span></td>
                <td class="px-5 py-3 text-xs">
                    @foreach ($p->detail as $d)
                        <div>{{ $d->lapangan->nama_lapangan }} &middot; {{ $d->tanggal_main }} {{ substr($d->jam_mulai, 0, 5) }}</div>
                    @endforeach
                </td>
                <td class="px-5 py-3">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                <td class="px-5 py-3">
                    @if ($p->transaksi?->bukti_bayar)
                        <a href="{{ asset('storage/bukti/' . $p->transaksi->bukti_bayar) }}" target="_blank" class="font-semibold text-teal-700 hover:underline">Lihat</a>
                    @else
                        -
                    @endif
                </td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('admin.verifikasi.setujui', $p) }}" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <button class="rounded-lg bg-teal-700 px-4 py-1.5 text-xs font-semibold text-white hover:bg-teal-800">Setujui</button>
                    </form>
                    <form method="POST" action="{{ route('admin.verifikasi.tolak', $p) }}" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="catatan_batal" placeholder="Alasan penolakan" required
                               class="w-40 rounded-lg border border-slate-200 px-3 py-1.5 text-xs outline-none focus:border-red-500">
                        <button class="rounded-lg bg-red-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Tolak</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Tidak ada pembayaran yang menunggu verifikasi.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $pesanan->links() }}</div>
@endsection
