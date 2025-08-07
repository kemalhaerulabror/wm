<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules;

class ResetPasswordController extends Controller
{
    /**
     * Tampilkan form reset password.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('user.auth.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Reset password pengguna.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'token.required' => 'Token tidak valid',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password baru harus diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Proses reset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        // Kembalikan status sesuai hasil operasi
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password Anda telah berhasil diubah')
            : back()->withErrors(['email' => [__($status)]]);
    }
} 