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
}
