<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice', 'customer_name', 'table_id', 'table_number', 'cashier_id',
        'voucher_id', 'subtotal', 'discount_amount', 'tax_amount', 'grand_total',
        'payment_method', 'payment_amount', 'change_amount', 'payment_status',
        'status', 'notes'
    ];

    // Relasi ke Kasir
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    // Relasi ke Meja
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    // Relasi ke Item Transaksi (1 Transaksi punya banyak Item)
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
