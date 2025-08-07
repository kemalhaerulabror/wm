<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna
     */
    public function index()
    {
        $user = Auth::user();
        return view('user.profile.index', compact('user'));
    }
    
    /**
     * Menampilkan daftar pesanan pengguna
     */
    public function orders(Request $request)
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('user.profile.orders', compact('orders'));
    }
    
    /**
     * Menampilkan halaman edit profil
     */
    public function edit()
    {
        $user = Auth::user();
        return view('user.profile.edit', compact('user'));
    }
    
    /**
     * Update profil pengguna
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
        ]);
        
        DB::table('users')
            ->where('id', Auth::id())
            ->update([
                'name' => $request->name,
                'phone' => $request->phone,
            ]);
        
        session()->flash('success', 'Profil berhasil diperbarui');
        
        return redirect()->route('profile.index');
    }
    
    /**
     * Menampilkan halaman ganti password
     */
    public function changePassword()
    {
        return view('user.profile.change-password');
    }
    
    /**
     * Update password pengguna
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        DB::table('users')
            ->where('id', Auth::id())
            ->update([
                'password' => Hash::make($request->password),
            ]);
        
        session()->flash('success', 'Password berhasil diperbarui');
        
        return redirect()->route('profile.index');
    }
    
    /**
     * Konfirmasi pesanan selesai oleh user
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmOrderCompletion(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->where('user_confirmed', false)
            ->firstOrFail();
            
        $order->user_confirmed = true;
        $order->user_confirmed_at = now();
        $order->save();
        
        return redirect()->route('profile.orders')
            ->with('success', 'Pesanan berhasil dikonfirmasi selesai. Terima kasih telah berbelanja!');
    }
} 