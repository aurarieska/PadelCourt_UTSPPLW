<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } }</script>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

<header class="border-b border-slate-100 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <a href="{{ route('katalog.index') }}" class="text-xl font-extrabold text-teal-800">{{ config('app.name') }}</a>

        <nav class="flex items-center gap-5 text-sm font-semibold text-slate-600">
            <a href="{{ route('katalog.index') }}" class="{{ request()->routeIs('katalog.*') ? 'text-teal-700' : 'hover:text-teal-700' }}">Katalog</a>

            @auth
                @php $jumlahKeranjang = \App\Models\Keranjang::where('id_pengguna', auth()->id())->count(); @endphp
                <a href="{{ route('pelanggan.dashboard') }}" class="{{ request()->routeIs('pelanggan.dashboard') ? 'text-teal-700' : 'hover:text-teal-700' }}">Akun</a>
                <a href="{{ route('pelanggan.pesanan.index') }}" class="{{ request()->routeIs('pelanggan.pesanan.*') ? 'text-teal-700' : 'hover:text-teal-700' }}">Pesanan Saya</a>
                <a href="{{ route('pelanggan.keranjang.index') }}" class="{{ request()->routeIs('pelanggan.keranjang.*') ? 'text-teal-700' : 'hover:text-teal-700' }}">
                    Keranjang @if ($jumlahKeranjang) <span class="ml-1 rounded-full bg-teal-700 px-2 py-0.5 text-xs text-white">{{ $jumlahKeranjang }}</span> @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-red-600 hover:underline">Keluar ({{ auth()->user()->nama_pengguna }})</button>
                </form>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="hover:text-teal-700">Login</a>
                <a href="{{ route('register') }}" class="rounded-xl bg-teal-700 px-4 py-2 text-white hover:bg-teal-800">Daftar</a>
            @endguest
        </nav>
    </div>
</header>

<main class="mx-auto max-w-6xl px-6 py-8">
    @if (session('sukses'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('sukses') }}</div>
    @endif
    @if (session('galat'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-600">{{ session('galat') }}</div>
    @endif
    @if ($errors->any() && ! View::hasSection('tanpa-galat'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
            @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    @yield('content')
</main>

<footer class="py-8 text-center text-xs text-slate-400">&copy; {{ date('Y') }} {{ config('app.name') }}</footer>
@stack('scripts')
</body>
</html>
