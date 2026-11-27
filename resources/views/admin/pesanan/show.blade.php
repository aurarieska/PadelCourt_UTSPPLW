@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $pesanan->kode_pesanan)

@section('content')
@php
$t = $pesanan->transaksi;
$berakhir = $pesanan->sudahBerakhir();
$akhirBermain = $pesanan->waktuSelesaiBermain();
@endphp

<a href="{{ route('admin.pesanan.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-teal-700">
    <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali ke daftar pesanan
</a>

<div class="mt-4 flex items-center justify-between">
    <h1 class="text-3xl font-extrabold text-slate-900">Detail Pesanan</h1>
    <span class="rounded-lg bg-white px-3 py-2 text-xs font-medium text-slate-500 shadow-sm">
        Dibuat pada {{ $pesanan->tanggal_pesan->translatedFormat('d M Y, H:i') }} WIB
    </span>
</div>

<div class="mt-6 grid grid-cols-2 gap-6">
    {{-- Rincian pesanan --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 font-bold"><i data-lucide="clipboard-list" class="h-4 w-4 text-teal-700"></i> Rincian Pesanan</h2>
            @include('partials.status-badge', ['status' => $pesanan->status_pesanan])
        </div>

        <div class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-4 text-sm">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Kode Pesanan</p>
                <p class="mt-1 font-bold">{{ $pesanan->kode_pesanan }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Jenis Pesanan</p>
                <p class="mt-1 font-bold">{{ $pesanan->jenis_pesanan === 'walk-in' ? 'Walk-in' : 'Daring' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Customer</p>
                <p class="mt-1 font-bold">{{ $pesanan->nama_pemesan }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">No. HP</p>
                <p class="mt-1 font-bold">{{ $pesanan->no_telepon_pemesan }}</p>
            </div>
        </div>

        <p class="mt-5 text-[10px] font-semibold uppercase tracking-wide text-slate-400">
            Sesi lapangan dipilih ({{ $pesanan->detail->count() }})
        </p>
        <div class="mt-2 space-y-2">
            @foreach ($pesanan->detail->sortBy(fn ($d) => $d->tanggal_main . $d->jam_mulai) as $d)
            <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 {{ $d->aktif ? '' : 'opacity-60' }}">
                <div>
                    <p class="text-sm font-bold">{{ $d->lapangan->nama_lapangan }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">
                        {{ \Carbon\Carbon::parse($d->tanggal_main)->translatedFormat('d M Y') }}
                        · {{ substr($d->jam_mulai, 0, 5) }} - {{ \Carbon\Carbon::parse($d->jam_mulai)->addHour()->format('H:i') }}
                        @unless ($d->aktif) · jadwal dilepas @endunless
                    </p>
                </div>
                <p class="font-bold text-teal-800">Rp{{ number_format($d->harga_satuan, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>

        <div class="mt-4 flex items-center justify-between rounded-xl bg-teal-50 px-5 py-4">
            <div>
                <p class="font-bold">Total Harga</p>
                <p class="text-xs text-slate-500">{{ $pesanan->detail->count() }} sesi</p>
            </div>
            <p class="text-2xl font-extrabold text-teal-800">Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Pembayaran + aksi --}}
    <div class="space-y-6">
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="flex items-center gap-2 font-bold"><i data-lucide="wallet" class="h-4 w-4 text-teal-700"></i> Pembayaran</h2>

            @if ($t)
            <div class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-4 text-sm">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Metode</p>
                    <p class="mt-1 font-bold">{{ $t->metode_pembayaran === 'QRIS' ? 'QRIS' : 'Tunai' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Jumlah Bayar</p>
                    <p class="mt-1 font-bold">Rp{{ number_format($t->jumlah_bayar, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Tanggal Bayar</p>
                    <p class="mt-1 font-bold">{{ $t->tanggal_bayar->translatedFormat('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Diverifikasi Oleh</p>
                    <p class="mt-1 font-bold">{{ $t->admin->nama_pengguna ?? '-' }}</p>
                </div>
            </div>

            <p class="mt-5 text-[10px] font-semibold uppercase tracking-wide text-slate-400">Bukti Pembayaran</p>
            @if ($t->bukti_bayar)
            <img src="{{ asset('storage/bukti/' . $t->bukti_bayar) }}" alt="Bukti pembayaran"
                class="mt-2 max-h-80 w-full rounded-xl object-contain bg-slate-50">
            @else
            <div class="mt-2 rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-400">
                Tanpa bukti unggahan (transaksi tatap muka)
            </div>
            @endif
            @else
            <div class="mt-4 rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-400">
                Belum ada transaksi. Bukti pembayaran belum diunggah.
            </div>
            @endif
        </div>

        {{-- Catatan pembatalan --}}
        @if ($pesanan->status_pesanan === 'batal')
        <div class="rounded-2xl border border-red-200 bg-red-50 p-6">
            <p class="text-sm font-bold text-red-700">
                {{ match ($pesanan->alasan_batal) {
                            'pelanggan' => 'Dibatalkan oleh pelanggan',
                            'ditolak' => 'Ditolak oleh admin',
                            'kedaluwarsa' => 'Melewati batas waktu pembayaran',
                            default => 'Pesanan dibatalkan',
                        } }}
            </p>
            @if ($pesanan->catatan_batal)
            <p class="mt-2 text-sm text-red-600">{{ $pesanan->catatan_batal }}</p>
            @endif
        </div>
        @endif

        {{-- Tandai selesai (KF-30) --}}
        @if ($pesanan->status_pesanan === 'terverifikasi')
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.pesanan.selesai', $pesanan) }}"
                onsubmit="return confirm('Tandai pesanan {{ $pesanan->kode_pesanan }} sebagai selesai?')">
                @csrf
                @method('PATCH')
                <button type="submit" @disabled(! $berakhir)
                    class="flex w-full items-center justify-center gap-2 rounded-xl py-3 text-sm font-bold
                                       {{ $berakhir ? 'bg-teal-700 text-white hover:bg-teal-800' : 'cursor-not-allowed bg-slate-200 text-slate-400' }}">
                    <i data-lucide="circle-check" class="h-4 w-4"></i> Tandai Selesai
                </button>
            </form>
            @unless ($berakhir)
            <p class="mt-3 text-center text-xs text-slate-500">
                Aktif setelah seluruh waktu bermain berakhir
                ({{ $akhirBermain->translatedFormat('d M Y, H:i') }} WIB).
            </p>
            @endunless
        </div>
        @elseif ($pesanan->status_pesanan === 'selesai')
        <div class="rounded-2xl bg-slate-100 p-4 text-center text-sm font-semibold text-slate-500">
            Pesanan ini sudah selesai.
        </div>
        @endif
    </div>
</div>
@endsection