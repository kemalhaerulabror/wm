<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AddToCartButton extends Component
{
    public $productId;
    public $buttonLabel = 'Tambah ke Keranjang';
    public $showQuantity = false;
    public $quantity = 1;
    public $stock = 0;
    
    public function mount($productId, $buttonLabel = null, $showQuantity = false)
    {
        $this->productId = $productId;
        
        if ($buttonLabel) {
            $this->buttonLabel = $buttonLabel;
        }
        
        $this->showQuantity = $showQuantity;
        
        // Ambil stok produk
        $product = Product::find($productId);
        if ($product) {
            $this->stock = $product->stock;
        }
    }
    
    // Tambah ke keranjang
    public function addToCart()
    {
        $product = Product::findOrFail($this->productId);
        
        if (!$product->status || $product->stock <= 0) {
            session()->flash('error', 'Produk tidak tersedia');
            return;
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
        
        $quantityToAdd = $this->showQuantity ? max(1, $this->quantity) : 1;
        
        if ($cartItem) {
            // Update jumlah jika produk sudah ada di keranjang
            $newQuantity = $cartItem->quantity + $quantityToAdd;
            
            if ($newQuantity > $product->stock) {
                session()->flash('error', 'Stok produk tidak mencukupi');
                return;
            }
            
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Tambahkan produk baru ke keranjang
            Cart::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $product->id,
                'quantity' => $quantityToAdd,
                'price' => $product->price
            ]);
        }
        
        // Emit event untuk memperbarui counter
        $this->dispatch('cartUpdated');
        
        session()->flash('success', 'Produk berhasil ditambahkan ke keranjang');
    }
    
    // Kurangi jumlah
    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }
    
    // Tambah jumlah
    public function increaseQuantity()
    {
        if ($this->quantity < $this->stock) {
            $this->quantity++;
        }
    }
    
    // Fungsi untuk mendapatkan atau membuat session ID untuk user yang tidak login
    private function getSessionId()
    {
        if (!session()->has('cart_session_id')) {
            session()->put('cart_session_id', Str::uuid()->toString());
        }
        return session('cart_session_id');
    }
    
    public function render()
    {
        return view('livewire.cart.add-to-cart-button');
    }
}
