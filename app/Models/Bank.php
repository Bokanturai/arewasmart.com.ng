<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'bankCode',
        'bankName',
        'bankUrl',
        'bgUrl',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
