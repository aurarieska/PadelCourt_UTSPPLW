@extends('layouts.pelanggan')

@section('title', $lapangan->nama_lapangan)

@section('content')
<a href="{{ route('katalog.index') }}" class="text-sm font-semibold text-teal-700 hover:underline">&larr; Kembali ke katalog</a>

<div class="mt-4 grid gap-8 lg:grid-cols-3">
    <div class="lg:col-span-1">
        <div class="flex h-56 items-center justify-center overflow-hidden rounded-2xl bg-slate-200 text-sm text-slate-400">
            @if ($lapangan->foto_url) <img src="{{ $lapangan->foto_url }}" alt="{{ $lapangan->nama_lapangan }}" class="h-full w-full object-cover">
            @else Belum ada foto @endif
        </div>
        <span class="mt-4 inline-block rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">{{ $lapangan->jenis?->nama_jenis ?? ucfirst($lapangan->jenis_lapangan) }}</span>
        <h1 class="mt-2 text-2xl font-extrabold">{{ $lapangan->nama_lapangan }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ $lapangan->deskripsi }}</p>
        <p class="mt-3 text-xl font-extrabold text-teal-800">Rp {{ number_format($lapangan->harga_per_sesi, 0, ',', '.') }} <span class="text-xs font-medium text-slate-500">/ sesi</span></p>
    </div>

    <div class="lg:col-span-2">
        <form method="GET" class="mb-5 flex items-center gap-2">
            <label for="tanggal" class="text-sm font-semibold">Tanggal main</label>
            <input id="tanggal" type="date" name="tanggal" value="{{ $tgl }}" min="{{ today()->toDateString() }}"
                class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm">
            <button class="rounded-xl bg-slate-800 px-5 py-2 text-sm font-semibold text-white">Lihat</button>
        </form>

        @guest
        <div class="mb-4 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700">
            <a href="{{ route('login') }}" class="font-semibold underline">Login</a> atau
            <a href="{{ route('register') }}" class="font-semibold underline">daftar</a> untuk memesan sesi.
        </div>
        @endguest

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
            @foreach ($jamSesi as $jam)
            @php
            $lewat = \Carbon\Carbon::parse($tgl . ' ' . $jam)->isPast();
            $label = substr($jam, 0, 2) . ':00 - ' . sprintf('%02d:00', (int) $jam + 1);
            @endphp

            @if (in_array($jam, $terisi))
            <div class="rounded-xl bg-red-50 px-3 py-3 text-center text-xs font-semibold text-red-500">{{ $label }}<br>Terisi</div>
            @elseif ($lewat)
            <div class="rounded-xl bg-slate-100 px-3 py-3 text-center text-xs font-semibold text-slate-400">{{ $label }}<br>Lewat</div>
            @elseif (isset($dikeranjang[$jam]))
            <form method="POST" action="{{ route('pelanggan.keranjang.destroy', $dikeranjang[$jam]) }}">
                @csrf
                @method('DELETE')
                <button class="w-full rounded-xl bg-amber-100 px-3 py-3 text-center text-xs font-semibold text-amber-700 hover:bg-amber-200">
                    {{ $label }}<br>Di keranjang
                </button>
            </form>
            @else
            @auth
            <form method="POST" action="{{ route('pelanggan.keranjang.store') }}">
                @csrf
                <input type="hidden" name="id_lapangan" value="{{ $lapangan->id_lapangan }}">
                <input type="hidden" name="tanggal_main" value="{{ $tgl }}">
                <input type="hidden" name="jam_mulai" value="{{ $jam }}">
                <button class="w-full rounded-xl bg-emerald-50 px-3 py-3 text-center text-xs font-semibold text-emerald-700 hover:bg-emerald-100">{{ $label }}<br>Tambah</button>
            </form>
            @else
            <div class="rounded-xl bg-emerald-50 px-3 py-3 text-center text-xs font-semibold text-emerald-700">{{ $label }}<br>Tersedia</div>
            @endauth
            @endif
            @endforeach
        </div>
    </div>
</div>
@endsection