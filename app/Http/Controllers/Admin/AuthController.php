<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        $credentials = $request->only('email', 'password');
        $email = $request->input('email');
        
        // Cek apakah email terdaftar sebagai admin
        $admin = Admin::where('email', $email)->first();
        
        if (!$admin) {
            return back()->withErrors([
                'email' => 'Email tidak terdaftar di sistem admin.',
            ])->withInput($request->only('email'));
        }
        
        // Cek role admin
        if ($admin->role !== 'admin') {
            return back()->withErrors([
                'role' => 'Anda bukan admin. Akses ditolak.',
            ])->withInput($request->only('email'));
        }
        
        // Cek password
        if (!Hash::check($request->password, $admin->password)) {
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ])->withInput($request->only('email'));
        }

        // Coba login
        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        // Jika masih gagal login
        return back()->withErrors([
            'login' => 'Terjadi kesalahan pada sistem autentikasi. Silakan hubungi administrator.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda berhasil logout dari sistem admin.');
    }
}
