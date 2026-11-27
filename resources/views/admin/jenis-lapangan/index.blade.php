@extends('layouts.admin')

@section('title', 'Jenis Lapangan')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-extrabold">Jenis Lapangan</h1>
    <a href="{{ route('admin.jenis-lapangan.create') }}" class="rounded-xl bg-teal-700 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-800">+ Tambah Jenis</a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama jenis..."
           class="w-72 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-teal-600">
    <button class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white">Cari</button>
</form>

<div class="overflow-hidden rounded-2xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-100 text-xs uppercase text-slate-500">
            <tr><th class="px-5 py-3">Nama Jenis</th><th class="px-5 py-3">Keterangan</th><th class="px-5 py-3">Jumlah Lapangan</th><th class="px-5 py-3 text-right">Aksi</th></tr>
        </thead>
        <tbody>
        @forelse ($jenis as $j)
            <tr class="border-t border-slate-100">
                <td class="px-5 py-3 font-semibold">{{ $j->nama_jenis }}</td>
                <td class="px-5 py-3 text-slate-600">{{ $j->keterangan ?? '-' }}</td>
                <td class="px-5 py-3">{{ $j->lapangan_count }}</td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.jenis-lapangan.edit', $j) }}" class="font-semibold text-teal-700 hover:underline">Ubah</a>
                        <form method="POST" action="{{ route('admin.jenis-lapangan.destroy', $j) }}" onsubmit="return confirm('Hapus jenis ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="font-semibold text-red-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-5 py-8 text-center text-slate-500">Belum ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $jenis->links() }}</div>
@endsection
