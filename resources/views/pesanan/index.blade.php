@extends('layouts.pelanggan')

@section('title', 'Pesanan Saya')

@section('content')
<h1 class="mb-6 text-2xl font-extrabold">Pesanan Saya</h1>

<div class="overflow-hidden rounded-2xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-100 text-xs uppercase text-slate-500">
            <tr><th class="px-5 py-3">Kode</th><th class="px-5 py-3">Tanggal Pesan</th><th class="px-5 py-3">Sesi</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"></th></tr>
        </thead>
        <tbody>
        @forelse ($pesanan as $p)
            <tr class="border-t border-slate-100">
                <td class="px-5 py-3 font-semibold">{{ $p->kode_pesanan }}</td>
                <td class="px-5 py-3">{{ $p->tanggal_pesan->format('d M Y H:i') }}</td>
                <td class="px-5 py-3">{{ $p->detail_count }}</td>
                <td class="px-5 py-3">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                <td class="px-5 py-3">@include('partials.badge-pesanan', ['status' => $p->status_pesanan])</td>
                <td class="px-5 py-3 text-right"><a href="{{ route('pelanggan.pesanan.show', $p) }}" class="font-semibold text-teal-700 hover:underline">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada pesanan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $pesanan->links() }}</div>
@endsection
