<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'quantity',
        'price'
    ];
    
    // Relasi dengan Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Hitung total harga untuk item ini (harga * kuantitas)
    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
