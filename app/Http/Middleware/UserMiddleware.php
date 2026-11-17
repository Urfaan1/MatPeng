<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Jika akun adalah admin, alihkan ke panel admin (admin hanya punya panel admin)
            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            // Jika akun belum diaktifkan oleh admin, putuskan sesi dan beri peringatan
            if (! Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Anda belum diaktifkan oleh Administrator. Silakan tunggu konfirmasi.',
                ]);
            }
        }

        return $next($request);
    }
}
