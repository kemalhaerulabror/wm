<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil produk yang dicentang "featured" dan berstatus aktif
        $featuredProducts = Product::where('is_featured', true)
            ->where('status', true)
            ->latest()
            ->limit(6)
            ->get();
            
        // Ambil 10 produk terbaru yang aktif untuk bagian "Semua Produk"
        $allProducts = Product::where('status', true)
            ->latest()
            ->limit(10)
            ->get();
            
        return view('user.welcome', [
            'title' => 'Wipa Motor - Dealer Motor Terpercaya',
            'featuredProducts' => $featuredProducts,
            'allProducts' => $allProducts
        ]);
    }
} 