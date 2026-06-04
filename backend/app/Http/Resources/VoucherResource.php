<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'discount_value' => $this->discount_value,
            'min_purchase' => $this->min_purchase,
            'max_discount' => $this->max_discount,
            'quota' => $this->quota,
            'used_count' => $this->used_count,
            'expired_at' => $this->expired_at->format('Y-m-d H:i:s'),
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
