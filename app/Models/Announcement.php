<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'is_active',
        'type',
        'status',
        'image',
        'discount',
        'service_name',
        'link',
    ];

    /**
     * Get the active announcement.
     *
     * @return Announcement|null
     */
    public static function getActiveAnnouncement()
    {
        return self::where('type', 'announcement')
            ->where('status', 'active')
            ->where('is_active', true)
            ->latest()
            ->first();
    }

    /**
     * Get the active adverts.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveAdverts()
    {
        return self::where('type', 'advert')
            ->where('status', 'active')
            ->where('is_active', true)
            ->latest()
            ->get();
    }
}
