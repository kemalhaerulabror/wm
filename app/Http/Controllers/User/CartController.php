<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Menampilkan halaman keranjang
    public function index()
    {
        // Redirect ke login jika user belum login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses keranjang');
        }
        
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        
        $total = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        
        return view('user.cart.index', compact('cartItems', 'total'));
    }
    
    // Menambahkan produk ke keranjang
    public function addToCart(Request $request, $id)
    {
        // Redirect ke login jika user belum login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk menambahkan produk ke keranjang');
        }
        
        $product = Product::findOrFail($id);
        
        if (!$product->status || $product->stock <= 0) {
            return back()->with('error', 'Produk tidak tersedia');
        }
        
        $userId = Auth::id();
        
        // Cek apakah produk sudah ada di keranjang
        $cartItem = Cart::where('product_id', $product->id)
            ->where('user_id', $userId)
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
                'session_id' => null, // Tidak lagi menggunakan session
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
        // Redirect ke login jika user belum login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        $cartItem = Cart::findOrFail($id);
        
        // Pastikan pengguna hanya dapat menghapus item miliknya
        if ($cartItem->user_id != Auth::id()) {
            abort(403);
        }
        
        $cartItem->delete();
        
        return back()->with('success', 'Produk berhasil dihapus dari keranjang');
    }
    
    // Update jumlah item di keranjang
    public function updateQuantity(Request $request, $id)
    {
        // Redirect ke login jika user belum login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        $request->validate([
            'quantity' => 'required|integer'
        ]);
        
        $cartItem = Cart::findOrFail($id);
        
        // Pastikan pengguna hanya dapat mengupdate item miliknya
        if ($cartItem->user_id != Auth::id()) {
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
        }
        
        return 0;
    }
}