@php
    $warna = [
        'menunggu pembayaran' => 'bg-amber-100 text-amber-700',
        'menunggu verifikasi' => 'bg-sky-100 text-sky-700',
        'terverifikasi' => 'bg-emerald-100 text-emerald-700',
        'selesai' => 'bg-slate-200 text-slate-700',
        'batal' => 'bg-red-100 text-red-700',
    ];
@endphp
<span class="rounded-full px-3 py-1 text-xs font-semibold {{ $warna[$status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($status) }}</span>
