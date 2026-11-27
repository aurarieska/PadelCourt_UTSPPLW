@extends('layouts.pelanggan')

@section('title', 'Akun Saya')

@section('content')
<h1 class="text-2xl font-extrabold">Halo, {{ auth()->user()->nama_pengguna }}</h1>

<div class="mt-6 grid gap-4 sm:grid-cols-3">
    @foreach ([['Total pesanan', $total], ['Menunggu pembayaran', $menungguBayar], ['Menunggu verifikasi', $menungguVerifikasi]] as [$label, $nilai])
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-extrabold text-teal-800">{{ $nilai }}</p>
        </div>
    @endforeach
</div>

<div class="mt-8 rounded-2xl bg-white p-6 shadow-sm">
    <h2 class="font-bold">Jadwal main mendatang</h2>
    @forelse ($mendatang as $d)
        <p class="mt-3 text-sm">{{ $d->tanggal_main }} &middot; {{ substr($d->jam_mulai, 0, 5) }} &middot; <strong>{{ $d->lapangan->nama_lapangan }}</strong></p>
    @empty
        <p class="mt-3 text-sm text-slate-500">Belum ada jadwal. <a href="{{ route('katalog.index') }}" class="font-semibold text-teal-700 underline">Pesan sekarang</a></p>
    @endforelse
</div>
@endsection
