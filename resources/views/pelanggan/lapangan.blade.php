<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Lapangan - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-50">
    <div class="max-w-md rounded-2xl bg-white p-10 text-center shadow-lg">
        <h1 class="text-2xl font-extrabold text-teal-800">Halo, {{ auth()->user()->nama_pengguna }}</h1>
        <p class="mt-3 text-sm text-slate-500">Halaman pelanggan (daftar lapangan, keranjang, checkout) belum termasuk dalam tahap pengerjaan ini.</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button class="rounded-xl bg-lime-400 px-6 py-2 text-sm font-bold text-lime-950">Logout</button>
        </form>
    </div>
</body>
</html>
