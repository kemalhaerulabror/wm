<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationThrottle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'attempts',
        'last_sent_at',
        'next_available_at',
    ];

    protected $casts = [
        'last_sent_at' => 'datetime',
        'next_available_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 