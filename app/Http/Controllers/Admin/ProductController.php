<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    // Menggunakan data statis, tetapi lebih baik disimpan di database atau config
    protected $categories = ['Motor Matic', 'Motor Bebek', 'Motor Sport'];
    protected $brands = ['Honda', 'Yamaha', 'Suzuki', 'Kawasaki'];

    public function index()
    {
        // Hanya me-render view, karena data produk akan diambil oleh Livewire
        return view('admin.products.index');
    }

    public function create()
    {
        $categories = $this->categories;
        $brands = $this->brands;
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', $this->categories),
            'brand' => 'required|string|in:' . implode(',', $this->brands),
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean',
            'is_featured' => 'boolean'
        ], [
            'category.in' => 'Kategori yang dipilih tidak valid',
            'brand.in' => 'Brand yang dipilih tidak valid',
        ]);

        $data = $request->except('image');
        
        // Perbaikan: Menggunakan `request()->boolean()` yang lebih ringkas dan aman
        $data['is_featured'] = $request->boolean('is_featured');
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = date('YmdHi') . '_' . $image->getClientOriginalName();
            
            // Perbaikan: Gunakan ImageManager untuk memproses dan meng-encode gambar
            $manager = new ImageManager(new Driver());
            $imageFile = $manager->read($image);
            $imageFile->scale(width: 800);
            
            // Perbaikan: Simpan gambar menggunakan Facade Storage
            Storage::disk('public')->put('product_images/' . $filename, $imageFile->encode());
            
            $data['image'] = $filename;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Motor berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        $categories = $this->categories;
        $brands = $this->brands;
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', $this->categories),
            'brand' => 'required|string|in:' . implode(',', $this->brands),
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean',
            'is_featured' => 'boolean'
        ], [
            'category.in' => 'Kategori yang dipilih tidak valid',
            'brand.in' => 'Brand yang dipilih tidak valid',
        ]);

        $data = $request->except('image');
        
        // Perbaikan: Menggunakan `request()->boolean()` yang lebih ringkas dan aman
        $data['is_featured'] = $request->boolean('is_featured');
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = date('YmdHi') . '_' . $image->getClientOriginalName();
            
            // Perbaikan: Hapus gambar lama menggunakan Facade Storage sebelum menyimpan yang baru
            if ($product->image) {
                Storage::disk('public')->delete('product_images/' . $product->image);
            }
            
            // Perbaikan: Gunakan ImageManager untuk memproses dan meng-encode gambar
            $manager = new ImageManager(new Driver());
            $imageFile = $manager->read($image);
            $imageFile->scale(width: 800);
            
            // Perbaikan: Simpan gambar baru menggunakan Facade Storage
            Storage::disk('public')->put('product_images/' . $filename, $imageFile->encode());
            
            $data['image'] = $filename;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        // Perbaikan: Hapus gambar menggunakan Facade Storage
        if ($product->image) {
            Storage::disk('public')->delete('product_images/' . $product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}