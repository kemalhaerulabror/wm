<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'payment_status',
        'payment_method',
        'payment_code',
        'payment_url',
        'status',
        'user_confirmed',
        'user_confirmed_at',
        'created_by_admin_id',
        'customer_name',
        'customer_email',
        'customer_phone',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'user_confirmed' => 'boolean',
        'user_confirmed_at' => 'datetime',
    ];

    // Relasi dengan user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan order items
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relasi dengan admin yang membuat pesanan
    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    // Format harga
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    // Status pesanan
    public static function statuses()
    {
        return [
            'pending' => 'Menunggu Pembayaran',
            'processing' => 'Diproses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    }

    public function getStatusLabelAttribute()
    {
        $statuses = self::statuses();
        return $statuses[$this->status] ?? 'Status Tidak Diketahui';
    }
    
    // Menentukan apakah perlu konfirmasi dari user
    public function getNeedsUserConfirmationAttribute()
    {
        return $this->status === 'completed' && 
               $this->payment_status === 'paid' && 
               !$this->user_confirmed;
    }
    
    // Menentukan apakah pesanan dibuat oleh admin
    public function getIsCreatedByAdminAttribute()
    {
        return !is_null($this->created_by_admin_id);
    }
} 