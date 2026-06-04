<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'type', 'discount_value', 'min_purchase',
        'max_discount', 'quota', 'used_count', 'expired_at', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
