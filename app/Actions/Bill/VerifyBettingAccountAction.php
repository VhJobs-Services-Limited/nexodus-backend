<?php

declare(strict_types=1);

namespace App\Actions\Bill;

use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\VerifyBettingAccountDto;

final class VerifyBettingAccountAction
{
    public function __construct(protected BillProviderInterface $provider) {}

    public function handle(VerifyBettingAccountDto $dto): string|bool
    {
        $response = $this->provider->verifyBettingAccountId($dto->provider_id, $dto->account_id);
        $customerName = trim($response->get('customer_name'));

        if (str_contains($customerName, 'Error') || empty($customerName)) {
            return false;
        }

        return $customerName;
    }
}
