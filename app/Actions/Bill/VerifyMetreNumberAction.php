<?php

namespace App\Actions\Bill;

use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\VerifyMetreNumberDto;

class VerifyMetreNumberAction
{
    public function __construct(protected BillProviderInterface $provider)
    {
    }

    public function handle(VerifyMetreNumberDto $dto): mixed
    {
        $response = $this->provider->verifyMetreNumber($dto->provider_id, $dto->metre_number);
        return trim($response->get('customer_name')) ?: "Invalid metre number";
    }
}
