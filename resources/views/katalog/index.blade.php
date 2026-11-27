@extends('layouts.pelanggan')

@section('title', 'Katalog Lapangan')

@section('content')
<h1 class="text-2xl font-extrabold">Katalog Lapangan Padel</h1>
<p class="mt-1 text-sm text-slate-500">Pilih lapangan, tentukan tanggal dan sesi, lalu pesan.</p>

<form method="GET" class="my-6 flex flex-wrap gap-2">
    <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama lapangan..."
           class="w-64 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-teal-600">
    <select name="jenis" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
        <option value="">Semua jenis</option>
        @foreach ($daftarJenis as $j)
            <option value="{{ $j->id_jenis }}" @selected($idJenis == $j->id_jenis)>{{ $j->nama_jenis }}</option>
        @endforeach
    </select>
    <button class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white">Cari</button>
</form>

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($lapangan as $l)
        <a href="{{ route('katalog.show', $l) }}" class="overflow-hidden rounded-2xl bg-white shadow-sm transition hover:shadow-md">
            <div class="flex h-44 items-center justify-center bg-slate-200 text-sm text-slate-400">
                @if ($l->foto_url) <img src="{{ $l->foto_url }}" alt="{{ $l->nama_lapangan }}" class="h-full w-full object-cover">
                @else Belum ada foto @endif
            </div>
            <div class="p-5">
                <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">{{ $l->jenis?->nama_jenis ?? ucfirst($l->jenis_lapangan) }}</span>
                <h2 class="mt-3 font-bold">{{ $l->nama_lapangan }}</h2>
                <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $l->deskripsi }}</p>
                <p class="mt-3 font-extrabold text-teal-800">Rp {{ number_format($l->harga_per_sesi, 0, ',', '.') }} <span class="text-xs font-medium text-slate-500">/ sesi (1 jam)</span></p>
            </div>
        </a>
    @empty
        <p class="col-span-full py-10 text-center text-slate-500">Lapangan tidak ditemukan.</p>
    @endforelse
</div>

<div class="mt-6">{{ $lapangan->links() }}</div>
@endsection
