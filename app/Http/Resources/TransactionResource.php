<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'description' => $this->description,
            'transaction_type' => $this->transaction_type,
            'status' => $this->status,
            'amount' => $this->amount,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'bill_details' => $this->whenLoaded('billTransaction', fn () => [
                'provider_reference' => $this->billTransaction->provider_reference,
                'type' => $this->billTransaction->type,
                'status' => $this->billTransaction->status,
                'payload' => $this->billTransaction->payload,
            ]),
        ];
    }
}
