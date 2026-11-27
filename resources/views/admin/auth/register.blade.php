<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="flex min-h-screen items-center justify-center bg-slate-50 font-sans text-slate-800">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-extrabold text-teal-800">Daftar Akun</h1>
        <p class="mt-1 text-sm text-slate-500">Buat akun pelanggan {{ config('app.name') }}.</p>

        <form method="POST" action="{{ route('register.proses') }}" class="mt-6 space-y-4">
            @csrf
            @foreach ([
            ['nama_pengguna', 'Nama Lengkap', 'text'],
            ['email', 'Email', 'email'],
            ['no_telepon', 'No. Telepon', 'text'],
            ['password', 'Kata Sandi', 'password'],
            ['password_confirmation', 'Ulangi Kata Sandi', 'password'],
            ] as [$name, $label, $type])
            <div>
                <label for="{{ $name }}" class="text-sm font-semibold">{{ $label }}</label>

                <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}"
                    autocomplete="{{ ['nama_pengguna' => 'name', 'email' => 'email', 'no_telepon' => 'tel', 'password' => 'new-password', 'password_confirmation' => 'new-password'][$name] }}"
                    value="{{ $type === 'password' ? '' : old($name) }}"
                    class="mt-1 w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm outline-none focus:border-teal-600 {{ $errors->has($name) ? 'border-red-500' : 'border-slate-200' }}">

                @error($name)
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            @endforeach

            <button type="submit" class="w-full rounded-xl bg-teal-700 py-3 text-sm font-semibold text-white hover:bg-teal-800">
                Daftar
            </button>
        </form>

        <p class="mt-5 text-center text-sm text-slate-500">
            Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-teal-700 hover:underline">Login</a>
        </p>
    </div>
</body>

</html>