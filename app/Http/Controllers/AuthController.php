<?php

namespace App\Http\Controllers;

use App\Models\ProfilPelapor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login', ['title' => 'Masuk - RuangAman']);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/')->with('success', 'Selamat datang kembali.');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi salah.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register', ['title' => 'Daftar - RuangAman']);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_tampilan' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'alias' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $rolePelapor = Role::query()->where('nama_role', 'Pelapor')->firstOrFail();

        $user = User::query()->create([
            'role_id' => $rolePelapor->role_id,
            'nama_tampilan' => $data['nama_tampilan'],
            'email' => $data['email'],
            'password_hash' => $data['password'],
            'status' => 'aktif',
        ]);

        ProfilPelapor::query()->create([
            'user_id' => $user->user_id,
            'alias' => $data['alias'],
            'kontak_opsional' => null,
            'preferensi_anonim' => true,
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Akun berhasil dibuat. Selamat datang di RuangAman.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Anda telah keluar.');
    }
}
