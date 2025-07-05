<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartCounter extends Component
{
    public $cartCount = 0;
    
    protected $listeners = ['cartUpdated' => 'updateCartCount'];
    
    public function mount()
    {
        $this->updateCartCount();
    }
    
    public function updateCartCount()
    {
        // Jika user login, ambil jumlah barang di keranjangnya
        if (Auth::check()) {
            $this->cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
        } 
        // Jika user belum login, gunakan session_id
        else {
            if (session()->has('cart_session_id')) {
                $sessionId = session('cart_session_id');
                $this->cartCount = Cart::where('session_id', $sessionId)->sum('quantity');
            } else {
                $this->cartCount = 0;
            }
        }
    }
    
    public function render()
    {
        return view('livewire.cart.cart-counter');
    }
}
