<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->whenLoaded('profile', fn() => $this->profile->name),
            'phone' => $this->whenLoaded('profile', fn() => $this->profile->phone),
            'role' => $this->whenLoaded('profile', fn() => $this->profile->role),
            'is_active' => $this->whenLoaded('profile', fn() => (bool) $this->profile->is_active),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
