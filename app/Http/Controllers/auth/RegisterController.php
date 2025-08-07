<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
// use App\Notifications\VerifyEmailNotification; // Tidak lagi diperlukan jika menggunakan notifikasi bawaan Laravel
use Illuminate\Auth\Events\Registered; // Pastikan ini di-use
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\URL; // Tidak lagi diperlukan jika menggunakan notifikasi bawaan Laravel
// use Illuminate\Support\Facades\Notification; // Tidak lagi diperlukan jika menggunakan notifikasi bawaan Laravel
use Illuminate\Support\Facades\Auth; // Tambahkan ini jika Anda ingin user langsung login setelah register

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('user.auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required',
        ], [
            'name.required' => 'Nama lengkap harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone.required' => 'Nomor telepon harus diisi',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'terms.required' => 'Anda harus menyetujui syarat dan ketentuan',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Opsional: Login user secara otomatis setelah registrasi
        // Jika Anda ingin pengguna langsung login setelah registrasi, aktifkan baris ini.
        // Auth::login($user);

        // --- BAGIAN PENTING UNTUK PENGIRIMAN EMAIL REAL-TIME ---
        // Panggil event Registered. Ini akan memicu pengiriman notifikasi verifikasi email
        // secara otomatis oleh Laravel jika model User mengimplementasikan MustVerifyEmail.
        // Ini adalah cara standar dan paling andal untuk mengirim email verifikasi.
        event(new Registered($user));
        // --- AKHIR BAGIAN PENTING ---

        // Tetap kembali ke halaman sebelumnya (halaman register) dengan pesan sukses.
        // Ini sesuai dengan permintaan Anda untuk tidak mengubah tampilan/redirect.
        return back()->with('success', 'Pendaftaran berhasil! Link verifikasi telah dikirim ke email Anda. Silakan cek email untuk verifikasi akun.');
    }
}
