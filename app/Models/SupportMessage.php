<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'message',
        'attachment',
        'is_admin_reply',
    ];

    protected $appends = ['attachment_url'];

    public function getAttachmentUrlAttribute()
    {
        if (!$this->attachment) {
            return null;
        }
        
        // If it's already a full URL, return it
        if (filter_var($this->attachment, FILTER_VALIDATE_URL)) {
            return $this->attachment;
        }

        return \Illuminate\Support\Facades\Storage::url($this->attachment);
    }

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
