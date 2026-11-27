@extends('layouts.pelanggan')

@section('title', 'Keranjang')

@section('content')
<h1 class="mb-6 text-2xl font-extrabold">Keranjang</h1>

<div class="overflow-hidden rounded-2xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-100 text-xs uppercase text-slate-500">
            <tr><th class="px-5 py-3">Lapangan</th><th class="px-5 py-3">Tanggal</th><th class="px-5 py-3">Sesi</th><th class="px-5 py-3">Harga</th><th class="px-5 py-3"></th></tr>
        </thead>
        <tbody>
        @forelse ($item as $i)
            <tr class="border-t border-slate-100">
                <td class="px-5 py-3 font-semibold">{{ $i->lapangan->nama_lapangan }}</td>
                <td class="px-5 py-3">{{ $i->tanggal_main }}</td>
                <td class="px-5 py-3">{{ substr($i->jam_mulai, 0, 5) }} - {{ sprintf('%02d:00', (int) $i->jam_mulai + 1) }}</td>
                <td class="px-5 py-3">Rp {{ number_format($i->harga_satuan, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right">
                    <form method="POST" action="{{ route('pelanggan.keranjang.destroy', $i) }}">
                        @csrf
                        @method('DELETE')
                        <button class="font-semibold text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">Keranjang kosong. <a href="{{ route('katalog.index') }}" class="font-semibold text-teal-700 underline">Pilih lapangan</a></td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@if ($item->isNotEmpty())
    <div class="mt-6 flex items-center justify-between rounded-2xl bg-white p-6 shadow-sm">
        <p class="text-lg font-extrabold">Total: Rp {{ number_format($total, 0, ',', '.') }}</p>
        <form method="POST" action="{{ route('pelanggan.checkout') }}">
            @csrf
            <button class="rounded-xl bg-teal-700 px-6 py-3 text-sm font-semibold text-white hover:bg-teal-800">Checkout</button>
        </form>
    </div>
@endif
@endsection
