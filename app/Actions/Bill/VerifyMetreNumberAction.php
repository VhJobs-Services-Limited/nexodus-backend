<?php

declare(strict_types=1);

namespace App\Actions\Bill;

use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\VerifyMetreNumberDto;

final class VerifyMetreNumberAction
{
    public function __construct(protected BillProviderInterface $provider) {}

    public function handle(VerifyMetreNumberDto $dto): string|bool
    {
        $response = $this->provider->verifyMetreNumber($dto->provider_id, $dto->metre_number);
        $customerName = trim($response->get('customer_name'));

        if (str_contains($customerName, 'invalid') || empty($customerName)) {
            return false;
        }

        return $customerName;
    }
}
