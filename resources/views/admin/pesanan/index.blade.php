@extends('layouts.admin')

@section('title', 'Semua Pesanan')

@section('content')
@php
$tabs = [
'semua' => 'Semua',
'menunggu-verifikasi' => 'Menunggu Verifikasi',
'terverifikasi' => 'Terverifikasi',
'selesai' => 'Selesai',
'batal' => 'Batal',
];
@endphp

<h1 class="text-3xl font-extrabold text-slate-900">Semua Pesanan</h1>
<p class="mt-1 text-sm text-slate-500">Seluruh pesanan customer dan walk-in</p>

<div class="mt-6 flex items-center justify-between">
    {{-- Tab filter status --}}
    <div class="inline-flex rounded-xl bg-slate-200/70 p-1">
        @foreach ($tabs as $kunci => $label)
        <a href="{{ route('admin.pesanan.index', array_filter(['status' => $kunci, 'tanggal' => $tanggal])) }}"
            class="rounded-lg px-4 py-2 text-sm font-semibold {{ $tab === $kunci ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Filter tanggal pesan --}}
    <form method="GET" action="{{ route('admin.pesanan.index') }}" class="flex items-center gap-2">
        <input type="hidden" name="status" value="{{ $tab }}">
        <div class="flex items-center gap-2 rounded-xl bg-white px-4 py-2 shadow-sm">
            <i data-lucide="calendar" class="h-4 w-4 text-teal-700"></i>
            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                class="bg-transparent text-sm font-semibold outline-none">
        </div>
        @if ($tanggal)
        <a href="{{ route('admin.pesanan.index', ['status' => $tab]) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">Reset</a>
        @endif
    </form>
</div>

<div class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-100/70">
            <tr class="text-xs font-semibold text-slate-500">
                <th class="px-6 py-4">Kode Pesanan</th>
                <th class="px-6 py-4">Customer</th>
                <th class="px-6 py-4">Tanggal Pesan</th>
                <th class="px-6 py-4">Total</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($pesanan as $p)
            <tr>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.pesanan.show', $p) }}" class="font-bold text-teal-800 hover:underline">{{ $p->kode_pesanan }}</a>
                </td>
                <td class="px-6 py-4">
                    <p class="font-semibold">{{ $p->nama_pemesan }}</p>
                    @if ($p->jenis_pesanan === 'walk-in')
                    <p class="text-xs text-slate-400">Walk-in</p>
                    @endif
                </td>
                <td class="px-6 py-4 text-slate-600">{{ $p->tanggal_pesan->translatedFormat('d M Y') }}</td>
                <td class="px-6 py-4 font-bold">Rp{{ number_format($p->total_harga, 0, ',', '.') }}</td>
                <td class="px-6 py-4">@include('partials.status-badge', ['status' => $p->status_pesanan])</td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('admin.pesanan.show', $p) }}"
                        class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold hover:bg-slate-200">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400">Tidak ada pesanan yang cocok dengan filter.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $pesanan->links() }}</div>
@endsection