<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }} Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } }</script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

@php
    // [label, nama route, pola route aktif, ikon]
    // Route yang belum dibuat (tahap berikutnya) otomatis menjadi '#'
    $menu = [
        ['Dashboard', 'admin.dashboard', 'admin.dashboard', 'layout-dashboard'],
        ['Lapangan', 'admin.lapangan.index', 'admin.lapangan.*', 'trophy'],
        ['Jadwal', 'admin.jadwal.index', 'admin.jadwal.*', 'calendar'],
        ['Verifikasi', 'admin.verifikasi.index', 'admin.verifikasi.*', 'badge-check'],
        ['Pesanan Walk-in', 'admin.walkin.create', 'admin.walkin.*', 'user-plus'],
        ['Semua Pesanan', 'admin.pesanan.index', 'admin.pesanan.*', 'clipboard-list'],
    ];
@endphp

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 flex w-64 flex-col bg-white shadow-sm">
        <div class="flex items-center gap-3 px-6 py-6">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-700 text-white">
                <i data-lucide="trophy" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-lg font-extrabold leading-none text-teal-800">{{ config('app.name') }}</p>
                <p class="mt-1 text-[10px] font-semibold tracking-wider text-slate-500">ADMIN ARENA</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1 px-4">
            @foreach ($menu as [$label, $rute, $pola, $ikon])
                <a href="{{ Route::has($rute) ? route($rute) : '#' }}"
                   class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold
                          {{ request()->routeIs($pola) ? 'bg-teal-700 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    <i data-lucide="{{ $ikon }}" class="h-5 w-5"></i> {{ $label }}
                </a>
            @endforeach
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="px-4 pb-6">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50">
                <i data-lucide="log-out" class="h-5 w-5"></i> Logout
            </button>
        </form>
    </aside>

    {{-- Konten --}}
    <div class="ml-64 min-h-screen flex-1">
        <header class="flex h-16 items-center justify-between border-b border-slate-100 bg-white px-8">
            <span class="font-semibold">{{ config('app.name') }} Admin</span>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-semibold leading-none">{{ auth()->user()->nama_pengguna }}</p>
                    <p class="mt-1 text-xs text-slate-500">Administrator</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-teal-700 text-white">
                    <i data-lucide="user" class="h-5 w-5"></i>
                </div>
            </div>
        </header>

        <main class="p-8">
            @if (session('sukses'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('sukses') }}
                </div>
            @endif
            @if (session('galat'))
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-600">
                    {{ session('galat') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>lucide.createIcons();</script>
@stack('scripts')
</body>
</html>
