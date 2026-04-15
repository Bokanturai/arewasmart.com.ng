<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'bank_code',
        'bank_name',
        'bank_url',
        'bg_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Accessor for bankCode (maps to bank_code)
     */
    public function getBankCodeAttribute()
    {
        return $this->attributes['bank_code'] ?? null;
    }

    /**
     * Accessor for bankName (maps to bank_name)
     */
    public function getBankNameAttribute()
    {
        return $this->attributes['bank_name'] ?? null;
    }

    /**
     * Accessor for bankUrl (maps to bank_url)
     */
    public function getBankUrlAttribute()
    {
        return $this->attributes['bank_url'] ?? null;
    }

    /**
     * Accessor for bgUrl (maps to bg_url)
     */
    public function getBgUrlAttribute()
    {
        return $this->attributes['bg_url'] ?? null;
    }
}
