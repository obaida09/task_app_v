<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $usedAt = $this->used_at ? $this->used_at->format('Y-m-d H:i:s') : 'N/A';
        return [
            'id' => $this->id,
            'name' => $this->name,
            'CardNumber' => $this->card_number,
            'value' => $this->value,
            'status' => $this->status,
            'usedAt'  => $this->used_at,
            'createdAt'  => $this->created_at->format('Y/m/d H:i'),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
