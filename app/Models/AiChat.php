<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference',
        'type',
        'subject',
        'status',
        'role',
        'content',
        'attachment',
    ];

    /**
     * Scope for support tickets.
     */
    public function scopeSupport($query)
    {
        return $query->where('type', 'support');
    }

    /**
     * Scope for AI comments on transactions/services.
     */
    public function scopeComment($query)
    {
        return $query->where('type', 'comment');
    }

    /**
     * Get the user that owns the chat.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
