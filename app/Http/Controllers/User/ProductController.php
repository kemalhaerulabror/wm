<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function category($category = null, Request $request)
    {
        $query = Product::where('status', true);
        $title = 'Semua Produk';
        $sort = $request->input('sort', 'terbaru'); // Default: terbaru
        
        // Filter berdasarkan kategori
        if ($category && $category !== 'all') {
            $formattedCategory = str_replace('-', ' ', $category);
            $query->where('category', 'like', '%' . $formattedCategory . '%');
            $title = ucwords(str_replace('-', ' ', $category));
        }
        
        // Filter berdasarkan brand
        if ($request->has('brand')) {
            $brand = $request->brand;
            $query->where('brand', 'like', '%' . $brand . '%');
            $title = $title . ' - ' . ucwords($brand);
        }
        
        // Atur pengurutan berdasarkan parameter sort
        if ($sort === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            // Default: terbaru
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(20)->withQueryString();
        
        return view('user.search.index', [
            'products' => $products,
            'query' => $title,
            'sort' => $sort,
            'category' => $category
        ]);
    }
    
    public function detail($slug)
    {
        $product = Product::where('slug', $slug)->where('status', true)->firstOrFail();
        
        // Mendapatkan 6 produk acak selain produk yang sedang ditampilkan
        $randomProducts = Product::where('id', '!=', $product->id)
            ->where('status', true)
            ->inRandomOrder()
            ->take(6)
            ->get();
        
        return view('user.product.detail', [
            'product' => $product,
            'randomProducts' => $randomProducts
        ]);
    }
} 