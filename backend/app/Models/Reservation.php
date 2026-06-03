<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone',
        'table_id',
        'guest_count',
        'reserved_at',
        'expired_at',
        'notes',
        'dp_amount',
        'dp_status',
        'payment_method',
        'status',
    ];

    // Konversi kolom datetime menjadi object Carbon otomatis
    protected function casts(): array
    {
        return [
            'reserved_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    // Relasi ke Meja
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
