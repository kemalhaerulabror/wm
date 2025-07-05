<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    // Fungsi untuk mendapatkan atau membuat session ID untuk user yang tidak login
    private function getSessionId()
    {
        if (!session()->has('cart_session_id')) {
            session()->put('cart_session_id', Str::uuid()->toString());
        }
        return session('cart_session_id');
    }
    
    // Menampilkan halaman keranjang
    public function index()
    {
        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        } else {
            $sessionId = $this->getSessionId();
            $cartItems = Cart::where('session_id', $sessionId)->with('product')->get();
        }
        
        $total = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        
        return view('user.cart.index', compact('cartItems', 'total'));
    }
    
    // Menambahkan produk ke keranjang
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        if (!$product->status || $product->stock <= 0) {
            return back()->with('error', 'Produk tidak tersedia');
        }
        
        $userId = Auth::check() ? Auth::id() : null;
        $sessionId = Auth::check() ? null : $this->getSessionId();
        
        // Cek apakah produk sudah ada di keranjang
        $cartItem = Cart::where('product_id', $product->id)
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->first();
        
        if ($cartItem) {
            // Update jumlah jika produk sudah ada di keranjang
            $newQuantity = $cartItem->quantity + 1;
            
            if ($newQuantity > $product->stock) {
                return back()->with('error', 'Stok produk tidak mencukupi');
            }
            
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Tambahkan produk baru ke keranjang
            Cart::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price
            ]);
        }
        
        // Redirect ke halaman keranjang atau kembali ke halaman produk
        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }
    
    // Menghapus item dari keranjang
    public function removeFromCart($id)
    {
        $cartItem = Cart::findOrFail($id);
        
        // Pastikan pengguna hanya dapat menghapus item miliknya
        if (Auth::check() && $cartItem->user_id != Auth::id()) {
            abort(403);
        } elseif (!Auth::check() && $cartItem->session_id != session('cart_session_id')) {
            abort(403);
        }
        
        $cartItem->delete();
        
        return back()->with('success', 'Produk berhasil dihapus dari keranjang');
    }
    
    // Update jumlah item di keranjang
    public function updateQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer'
        ]);
        
        $cartItem = Cart::findOrFail($id);
        
        // Pastikan pengguna hanya dapat mengupdate item miliknya
        if (Auth::check() && $cartItem->user_id != Auth::id()) {
            abort(403);
        } elseif (!Auth::check() && $cartItem->session_id != session('cart_session_id')) {
            abort(403);
        }
        
        // Jika kuantitas 0 atau kurang, hapus item dari keranjang
        if ($request->quantity <= 0) {
            $cartItem->delete();
            return redirect()->route('cart.index')->with('success', 'Produk berhasil dihapus dari keranjang');
        }
        
        // Cek stok produk
        $product = Product::findOrFail($cartItem->product_id);
        if ($request->quantity > $product->stock) {
            return back()->with('error', 'Stok produk tidak mencukupi');
        }
        
        $cartItem->update(['quantity' => $request->quantity]);
        
        return back()->with('success', 'Jumlah produk berhasil diupdate');
    }
    
    // Menghitung jumlah item di keranjang (untuk Livewire)
    public static function getCartCount()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->sum('quantity');
        } else {
            if (session()->has('cart_session_id')) {
                $sessionId = session('cart_session_id');
                return Cart::where('session_id', $sessionId)->sum('quantity');
            }
        }
        
        return 0;
    }
}
