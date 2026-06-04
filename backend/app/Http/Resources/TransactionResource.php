<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice' => $this->invoice,
            'customer_name' => $this->customer_name,
            'table_number' => $this->table_number,
            'cashier_name' => $this->whenLoaded('cashier', fn() => $this->cashier->profile->name ?? 'Kasir'),

            // Rincian Kalkulasi
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'grand_total' => $this->grand_total,

            // Info Pembayaran
            'payment_method' => $this->payment_method,
            'payment_amount' => $this->payment_amount,
            'change_amount' => $this->change_amount,
            'payment_status' => $this->payment_status,
            'status' => $this->status,

            // Keranjang Belanja
            'items' => $this->whenLoaded('items'),

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
