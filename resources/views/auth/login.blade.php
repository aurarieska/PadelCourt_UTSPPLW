<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } }</script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="font-sans text-slate-800">
<div class="flex min-h-screen">

    {{-- Panel kiri --}}
    <div class="hidden w-1/2 flex-col justify-between bg-emerald-950 bg-cover bg-center p-16 text-white lg:flex"
         style="background-image: linear-gradient(to top, rgba(2,44,34,.95), rgba(2,44,34,.55)), url('{{ asset('images/login-bg.jpeg') }}');">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-600">
                <i data-lucide="trophy" class="h-6 w-6 text-lime-300"></i>
            </div>
            <span class="text-3xl font-extrabold">{{ config('app.name') }}</span>
        </div>

        <div>
            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold tracking-wider text-lime-300">
                <span class="h-2 w-2 rounded-full bg-lime-400"></span> PADEL BOOKING PLATFORM
            </span>
            <h1 class="mt-5 text-5xl font-extrabold leading-tight">Sewa Lapangan Padel<br>Jadi Lebih Mudah</h1>
            <p class="mt-4 text-lg text-white/80">Pilih sesi, bayar via QRIS, langsung main.</p>
        </div>
    </div>

    {{-- Panel kanan --}}
    <div class="flex w-full items-center justify-center bg-slate-50 p-8 lg:w-1/2">
        <div class="w-full max-w-md rounded-2xl bg-white p-10 shadow-lg">
            <h2 class="text-3xl font-extrabold text-slate-900">Masuk ke Akun</h2>
            <p class="mt-2 text-sm text-slate-500">Silakan masukkan detail akun Anda untuk melanjutkan pemesanan lapangan.</p>

            @if ($errors->has('login'))
                <div class="mt-6 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-600">
                    <i data-lucide="alert-circle" class="h-4 w-4"></i> {{ $errors->first('login') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.proses') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="email" class="text-sm font-semibold">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus
                           placeholder="nama@email.com"
                           class="mt-2 w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm outline-none focus:border-teal-600 {{ $errors->any() ? 'border-red-500' : 'border-slate-200' }}">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="text-sm font-semibold">Password</label>
                    <div class="relative mt-2">
                        <input id="password" type="password" name="password" placeholder="Masukkan password"
                               class="w-full rounded-xl border bg-slate-50 px-4 py-3 pr-12 text-sm outline-none focus:border-teal-600 {{ $errors->any() ? 'border-red-500' : 'border-slate-200' }}">
                        <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i data-lucide="eye" class="h-5 w-5"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-lime-400 py-3 text-sm font-bold text-lime-950 hover:bg-lime-300">
                    Login <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-600">
                Belum punya akun? <a href="#" class="font-bold text-lime-700">Daftar</a>
            </p>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
    lucide.createIcons();
</script>
</body>
</html>
