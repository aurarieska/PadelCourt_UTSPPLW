<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Pemakaian pada route: role:admin  atau  role:admin,pelanggan
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login'); // Guest dialihkan ke login
        }

        if (in_array($request->user()->role, $roles, true)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak.');
    }
}
