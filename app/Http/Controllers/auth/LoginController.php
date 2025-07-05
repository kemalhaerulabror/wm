<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('user.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Cek apakah email terdaftar
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email belum terdaftar'],
            ]);
        }

        // Cek apakah email sudah diverifikasi
        if ($user->email_verified_at === null) {
            return redirect()->route('login')
                ->with('error', 'Email Anda belum diverifikasi. Silakan cek email untuk link verifikasi.')
                ->withInput();
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        // Jika email terdaftar tapi password salah
        throw ValidationException::withMessages([
            'password' => ['Password yang Anda masukkan salah'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        // Simpan token untuk redirect
        $redirect = redirect('/');
        
        // Invalidasi dan regenerasi token setelah merencanakan redirect
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return $redirect;
    }

    /**
     * Logout user dengan metode GET (sebagai fallback)
     */
    public function logoutGet(Request $request)
    {
        // Pastikan user sudah login
        if (Auth::check()) {
            // Logout tanpa menginvalidasi sesi untuk menghindari masalah CSRF
            Auth::guard()->logout();
        }
        
        // Redirect ke halaman login
        return redirect()->route('login');
    }
} 