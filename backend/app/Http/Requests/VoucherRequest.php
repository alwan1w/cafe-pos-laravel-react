<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                // Validasi unik, abaikan ID ini saat proses update
                Rule::unique('vouchers', 'code')->ignore($this->voucher)
            ],
            'type' => 'required|in:fixed,percent',
            'discount_value' => 'required|integer|min:1',
            'min_purchase' => 'nullable|integer|min:0',
            'max_discount' => 'nullable|integer|min:1',
            'quota' => 'nullable|integer|min:1',
            'expired_at' => 'required|date|after:now',
            'is_active' => 'boolean',
        ];
    }
}
