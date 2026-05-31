<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => [
                'required',
                'string',
                'max:10',
                // Validasi unik, abaikan ID ini saat proses update
                Rule::unique('tables', 'number')->ignore($this->table)
            ],
            'capacity' => 'required|integer|min:1',
            'status' => 'nullable|in:available,occupied,reserved',
        ];
    }
}
