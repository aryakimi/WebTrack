<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // 2. Proses Login (Cek Admin dulu, kalau gagal cek Member)
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // LOGIKA KETAT: Email harus huruf kecil persis seperti di database
        if (strtolower($request->email) !== $request->email) {
            return back()->withErrors([
                'login_error' => 'Format email harus menggunakan huruf kecil semua!',
            ])->onlyInput('email');
        }

        // Coba login pakai guard 'admin' dulu
        // (Laravel otomatis ngecek password case-sensitive)
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin');
        }

        // Kalau gagal, coba login pakai guard 'member'
        if (Auth::guard('member')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/member');
        }

        // Kalau dua-duanya gagal (Email/Password salah)
        return back()->withErrors([
            'login_error' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('member')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}