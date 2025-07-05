<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'brand',
        'price',
        'description',
        'image',
        'stock',
        'sold',
        'rating',
        'is_featured',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'is_featured' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Mutator untuk menetapkan nama dan slug secara otomatis.
     * 
     * @param string $value
     */
    public function setNameAttribute($value)
    {
        // Menetapkan nama produk
        $this->attributes['name'] = $value;

        // Membuat slug dari nama produk
        $slug = Str::slug($value);

        // Memeriksa apakah slug sudah ada di database
        $slugExist = Product::where('slug', $slug)->exists();

        // Jika slug sudah ada, tambahkan angka atau string acak untuk membuatnya unik
        if ($slugExist) {
            $slug = $slug . '-' . Str::random(5); // Menambahkan string acak pada slug yang sudah ada
        }

        // Menetapkan slug
        $this->attributes['slug'] = $slug;
    }

    /**
     * Format harga produk dengan format Rupiah.
     * 
     * @return string
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Mendapatkan URL gambar produk.
     * 
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // Cek apakah gambar disimpan di direktori lama atau menggunakan storage
            $path = public_path('upload/product_images/' . $this->image);
            
            if (file_exists($path)) {
                return asset('upload/product_images/' . $this->image);
            } else {
                // Gunakan storage path jika file tidak ada di path lama
                return asset('storage/product_images/' . $this->image);
            }
        }
        
        // Gunakan placeholder sebagai fallback jika tidak ada gambar
        return 'https://via.placeholder.com/640x480.png?text=No+Image+Available';
    }
}
