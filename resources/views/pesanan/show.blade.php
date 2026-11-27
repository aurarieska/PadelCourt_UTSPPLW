@extends('layouts.pelanggan')

@section('title', 'Pesanan ' . $pesanan->kode_pesanan)

@section('content')
<a href="{{ route('pelanggan.pesanan.index') }}" class="text-sm font-semibold text-teal-700 hover:underline">&larr; Pesanan saya</a>

<div class="mt-4 rounded-2xl bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-extrabold">{{ $pesanan->kode_pesanan }}</h1>
        @include('partials.badge-pesanan', ['status' => $pesanan->status_pesanan])
    </div>

    <table class="mt-5 w-full text-left text-sm">
        <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2">Lapangan</th><th>Tanggal</th><th>Sesi</th><th class="text-right">Harga</th></tr></thead>
        <tbody>
        @foreach ($pesanan->detail as $d)
            <tr class="border-t border-slate-100">
                <td class="py-2 font-semibold">{{ $d->lapangan->nama_lapangan }}</td>
                <td>{{ $d->tanggal_main }}</td>
                <td>{{ substr($d->jam_mulai, 0, 5) }} - {{ sprintf('%02d:00', (int) $d->jam_mulai + 1) }}</td>
                <td class="text-right">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p class="mt-4 text-right text-lg font-extrabold">Total: Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
</div>

@if ($pesanan->status_pesanan === 'menunggu pembayaran')
    <div class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="font-bold">Pembayaran QRIS</h2>
        <p class="mt-1 text-sm text-slate-600">
            Bayar sebesar <strong>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong> sebelum
            <strong>{{ $pesanan->batas_bayar?->format('d M Y H:i') }}</strong>, lalu unggah bukti pembayaran.
        </p>
        <div class="my-4 flex h-40 w-40 items-center justify-center rounded-xl border border-dashed border-slate-300 text-xs text-slate-400">QRIS PadelCourt</div>

        <form method="POST" action="{{ route('pelanggan.pesanan.bayar', $pesanan) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
            @csrf
            <input type="file" name="bukti" accept="image/png,image/jpeg" required class="text-sm">
            <button class="rounded-xl bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Kirim Bukti</button>
        </form>

        <form method="POST" action="{{ route('pelanggan.pesanan.batal', $pesanan) }}" class="mt-4" onsubmit="return confirm('Batalkan pesanan ini?')">
            @csrf
            @method('PATCH')
            <button class="text-sm font-semibold text-red-600 hover:underline">Batalkan pesanan</button>
        </form>
    </div>
@elseif ($pesanan->status_pesanan === 'menunggu verifikasi')
    <div class="mt-6 rounded-xl bg-sky-50 px-4 py-3 text-sm text-sky-700">Bukti pembayaran sudah dikirim. Menunggu verifikasi admin.</div>
@elseif ($pesanan->status_pesanan === 'batal')
    <div class="mt-6 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600">
        Pesanan dibatalkan ({{ $pesanan->alasan_batal }}).
        @if ($pesanan->catatan_batal) Alasan admin: {{ $pesanan->catatan_batal }} @endif
    </div>
@elseif ($pesanan->status_pesanan === 'terverifikasi')
    <div class="mt-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">Pembayaran terverifikasi. Sampai jumpa di lapangan!</div>
@endif
@endsection
