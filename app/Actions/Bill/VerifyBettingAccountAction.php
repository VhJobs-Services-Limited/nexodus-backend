<?php

namespace App\Actions\Bill;

use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\VerifyBettingAccountDto;

class VerifyBettingAccountAction
{
    public function __construct(protected BillProviderInterface $provider)
    {
    }

    public function handle(VerifyBettingAccountDto $dto): string
    {
        $response = $this->provider->verifyBettingAccountId($dto->provider_id, $dto->account_id);
        logger()->info('Verify betting account response', ['response' => $response]);
        return $response->get('customer_name');
    }
}
