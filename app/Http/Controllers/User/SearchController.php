<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $sort = $request->input('sort', 'terbaru'); // Default: terbaru
        
        // Decode URL encoded query jika perlu
        if ($query) {
            $query = urldecode($query);
        }
        
        // Jika query kosong, redirect ke home
        if (empty($query)) {
            return redirect()->route('home');
        }
        
        $productsQuery = Product::where('name', 'like', '%' . $query . '%')
            ->where('status', 1); // hanya produk aktif
        
        // Atur pengurutan berdasarkan parameter sort
        if ($sort === 'terlama') {
            $productsQuery->orderBy('created_at', 'asc');
        } else {
            // Default: terbaru
            $productsQuery->orderBy('created_at', 'desc');
        }
        
        $products = $productsQuery->paginate(12)->withQueryString();
            
        return view('user.search.index', compact('products', 'query', 'sort'));
    }
}
