<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'related_model_type',
        'related_model_id',
        'link',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the notification as read
     */
    public function markAsRead()
    {
        $this->is_read = true;
        return $this->save();
    }

    /**
     * Scope query to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Get the related model (polymorphic)
     */
    public function related()
    {
        if (!$this->related_model_type || !$this->related_model_id) {
            return null;
        }

        $modelClass = 'App\\Models\\' . $this->related_model_type;
        if (!class_exists($modelClass)) {
            return null;
        }

        return $modelClass::find($this->related_model_id);
    }
}
