<?php

namespace App\Http\Controllers\Autentikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KontrolerAutentikasi extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Rate limiting
        $key = 'login.' . Str::lower($request->input('email')) . '.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Coba login dengan email atau username
        $credentials = $request->only('password');
        $input = $request->input('email');

        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $input;
        } else {
            $credentials['username'] = $input;
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Cek akun aktif
            if (!$user->is_aktif) {
                Auth::logout();
                RateLimiter::hit($key);
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi pemilik kos.',
                ]);
            }

            RateLimiter::clear($key);
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user);
        }

        RateLimiter::hit($key);
        throw ValidationException::withMessages([
            'email' => 'Email/username atau kata sandi tidak cocok.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    private function redirectBasedOnRole($user)
    {
        if ($user->isOwner()) {
            return redirect()->route('owner.dashboard');
        }
        return redirect()->route('tenant.dashboard');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = Auth::user();
        $user->update([
            'password'             => bcrypt($request->password),
            'wajib_ganti_password' => false,
        ]);

        return redirect()->route($user->isOwner() ? 'owner.dashboard' : 'tenant.dashboard')
            ->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
