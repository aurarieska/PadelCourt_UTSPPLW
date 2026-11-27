@php
    $warna = match ($status) {
        'menunggu pembayaran' => 'bg-sky-100 text-sky-700',
        'menunggu verifikasi' => 'bg-amber-100 text-amber-700',
        'terverifikasi' => 'bg-emerald-100 text-emerald-700',
        'selesai' => 'bg-slate-100 text-slate-600',
        'batal' => 'bg-red-100 text-red-600',
        default => 'bg-slate-100 text-slate-600',
    };
@endphp
<span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $warna }}">{{ ucwords($status) }}</span>
