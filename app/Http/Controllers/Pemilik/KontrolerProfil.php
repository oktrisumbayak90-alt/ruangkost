<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class KontrolerProfil extends Controller
{
    public function edit()
    {
        return view('owner.profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $nama    = $request->input('nama', $request->input('name'));
        $email   = $request->input('email');
        $telepon = $request->input('telepon', $request->input('phone'));

        $request->merge([
            'nama'    => $nama,
            'email'   => $email,
            'telepon' => $telepon,
        ]);

        $request->validate([
            'nama'    => 'required|string|max:100',
            'email'   => 'required|email|unique:pengguna,email,' . $user->id,
            'telepon' => 'nullable|string|max:20',
        ], [
            'nama.required'  => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan.',
        ]);

        $user->update([
            'nama'    => $nama,
            'email'   => $email,
            'telepon' => $telepon,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        $kataSandiSaatIni = $request->input('kata_sandi_saat_ini', $request->input('current_password'));
        $kataSandiBaru    = $request->input('kata_sandi', $request->input('password'));
        $konfirmasi       = $request->input('kata_sandi_confirmation', $request->input('password_confirmation'));

        $request->merge([
            'kata_sandi_saat_ini'     => $kataSandiSaatIni,
            'kata_sandi'              => $kataSandiBaru,
            'kata_sandi_confirmation' => $konfirmasi,
        ]);

        $request->validate([
            'kata_sandi_saat_ini' => 'required',
            'kata_sandi'          => ['required', 'confirmed', Password::min(8)],
        ], [
            'kata_sandi_saat_ini.required' => 'Kata sandi saat ini wajib diisi.',
            'kata_sandi.required'          => 'Kata sandi baru wajib diisi.',
            'kata_sandi.confirmed'         => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = auth()->user();

        if (!Hash::check($kataSandiSaatIni, $user->password)) {
            return back()->withErrors(['kata_sandi_saat_ini' => 'Kata sandi saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($kataSandiBaru)]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
