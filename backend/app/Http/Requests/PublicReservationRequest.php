<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Publik (Guest) diizinkan
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'table_id' => 'required|exists:tables,id',
            'guest_count' => 'required|integer|min:1',
            // Wajib format tanggal & jam, dan harus waktu di masa depan
            'reserved_at' => 'required|date_format:Y-m-d H:i|after:now',
            'notes' => 'nullable|string',
        ];
    }
}
