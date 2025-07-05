<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'message',
        'is_admin',
        'is_read'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_read' => 'boolean',
    ];

    /**
     * Mendapatkan user yang terkait dengan chat
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan admin yang terkait dengan chat
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
