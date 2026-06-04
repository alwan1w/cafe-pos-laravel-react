<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PosTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'table_id' => 'nullable|exists:tables,id',
            'voucher_code' => 'nullable|string|exists:vouchers,code',

            // Validasi Items (Keranjang)
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',

            // Pembayaran
            'payment_method' => 'required|in:cash,qris',
            'payment_amount' => 'required_if:payment_method,cash|integer|min:0',
        ];
    }
}
