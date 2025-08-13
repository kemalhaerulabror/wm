<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Cart;

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
            
            // Migrasi session cart ke user cart setelah login berhasil
            $this->migrateSessionCartToUser();
            
            return redirect()->intended(route('home'));
        }

        // Jika email terdaftar tapi password salah
        throw ValidationException::withMessages([
            'password' => ['Password yang Anda masukkan salah'],
        ]);
    }

    /**
     * Migrasi session cart ke user cart setelah login berhasil
     */
    protected function migrateSessionCartToUser()
    {
        // Cek apakah ada session cart
        if (session()->has('cart_session_id')) {
            $sessionId = session('cart_session_id');
            $sessionCartItems = Cart::where('session_id', $sessionId)->get();
            
            foreach ($sessionCartItems as $sessionItem) {
                // Cek apakah produk sudah ada di user cart
                $existingUserItem = Cart::where('user_id', Auth::id())
                                       ->where('product_id', $sessionItem->product_id)
                                       ->first();
                                       
                if ($existingUserItem) {
                    // Jika produk sudah ada di user cart, gabungkan quantity
                    $newQuantity = $existingUserItem->quantity + $sessionItem->quantity;
                    
                    // Validasi stock (optional - untuk mencegah over-booking)
                    if ($sessionItem->product && $newQuantity > $sessionItem->product->stock) {
                        $newQuantity = $sessionItem->product->stock;
                    }
                    
                    $existingUserItem->update(['quantity' => $newQuantity]);
                    
                    // Hapus session cart item
                    $sessionItem->delete();
                } else {
                    // Jika produk belum ada di user cart, pindahkan dari session ke user
                    $sessionItem->update([
                        'user_id' => Auth::id(),
                        'session_id' => null
                    ]);
                }
            }
            
            // Hapus session cart ID setelah migrasi selesai
            session()->forget('cart_session_id');
        }
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