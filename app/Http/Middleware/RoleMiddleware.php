<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Cek apakah akun aktif
        if (!$user->is_aktif) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi pemilik kos.']);
        }

        // Cek role / peran (mendukung nama 'pemilik' / 'penghuni' dan 'owner' / 'tenant')
        $allowedRoles = [];
        foreach ($roles as $r) {
            $allowedRoles[] = $r;
            if ($r === 'owner') $allowedRoles[] = 'pemilik';
            if ($r === 'tenant') $allowedRoles[] = 'penghuni';
            if ($r === 'pemilik') $allowedRoles[] = 'owner';
            if ($r === 'penghuni') $allowedRoles[] = 'tenant';
        }

        if (!in_array($user->peran, $allowedRoles) && !in_array($user->role, $allowedRoles)) {
            abort(403, 'Akses tidak diizinkan.');
        }

        // Cek wajib_ganti_password
        if (($user->wajib_ganti_password || $user->must_change_password) && !$request->routeIs('password.change', 'password.update.forced')) {
            return redirect()->route('password.change')
                ->with('warning', 'Anda harus mengganti kata sandi sebelum melanjutkan.');
        }

        return $next($request);
    }
}
