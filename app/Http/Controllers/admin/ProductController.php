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
        
        // Secara eksplisit menetapkan is_featured menjadi false jika tidak ada dalam request
        if (!isset($data['is_featured'])) {
            $data['is_featured'] = false;
        }
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = date('YmdHi') . '_' . $image->getClientOriginalName();
            
            // Gunakan storage untuk menyimpan gambar
            $path = storage_path('app/public/product_images');
            
            // Membuat direktori jika belum ada
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            // Simpan gambar menggunakan method baru
            $manager = new ImageManager(new Driver());
            $imageFile = $manager->read($image);
            $imageFile->scale(width: 800);
            $imageFile->save($path . '/' . $filename);
            
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
        
        // Secara eksplisit menetapkan is_featured menjadi false jika tidak ada dalam request
        if (!isset($data['is_featured'])) {
            $data['is_featured'] = false;
        }
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = date('YmdHi') . '_' . $image->getClientOriginalName();
            
            // Gunakan storage untuk menyimpan gambar
            $path = storage_path('app/public/product_images');
            
            // Membuat direktori jika belum ada
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            // Hapus gambar lama jika ada
            if ($product->image) {
                $oldImagePath = $path . '/' . $product->image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            // Simpan gambar baru menggunakan method baru
            $manager = new ImageManager(new Driver());
            $imageFile = $manager->read($image);
            $imageFile->scale(width: 800);
            $imageFile->save($path . '/' . $filename);
            
            $data['image'] = $filename;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        // Hapus gambar jika ada
        if ($product->image) {
            // Cek kedua lokasi penyimpanan
            $oldPath = public_path('upload/product_images/' . $product->image);
            $newPath = storage_path('app/public/product_images/' . $product->image);
            
            if (file_exists($oldPath)) {
                unlink($oldPath);
            } elseif (file_exists($newPath)) {
                unlink($newPath);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}
