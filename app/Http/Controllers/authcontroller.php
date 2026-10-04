<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Pengguna;

class AuthController extends Controller
{
    public function tampilLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $kredensial = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (! Auth::attempt($kredensial)) {
            return back()
                ->withErrors(['login' => 'Email atau password salah']) // KF-03
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('beranda');
    }

    // Tujuan ditentukan sistem dari role, pengguna tidak memilih role (KF-03)
    public function beranda(): RedirectResponse
    {
        /** @var Pengguna|null $pengguna */
        $pengguna = Auth::user();

        if (! $pengguna) {
            return redirect()->route('login');
        }

        return $pengguna->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('pelanggan.lapangan');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
