<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user ? $this->user->id : null;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            // Password wajib saat create (POST), opsional saat update (PUT/PATCH)
            'password' => $this->isMethod('post') ? 'required|string|min:6' : 'nullable|string|min:6',
            'phone' => 'required|string|max:20',
            'role' => 'required|in:admin,kasir,kitchen,bar,owner',
            'is_active' => 'boolean',
        ];
    }
}
