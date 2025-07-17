<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ulid' => $this->ulid,
            'fullname' => $this->fullname,
            'username' => $this->username,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'has_pin' => $this->pin ? true : false,
            'wallet_balance' => $this->wallet_balance,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'phone_verified_at' => $this->phone_verified_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
