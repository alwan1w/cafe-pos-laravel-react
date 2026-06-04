<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    // Kita matikan timestamps karena sudah terwakili oleh created_at di tabel transactions
    public $timestamps = false;

    protected $fillable = [
        'transaction_id', 'product_id', 'product_name', 'product_type',
        'qty', 'unit_price', 'subtotal', 'status', 'notes' // <-- Tambahkan status di sini
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
