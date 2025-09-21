<?php

declare(strict_types=1);

namespace App\Actions\Bill;

use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\VerifyCableSmartCardDto;

final class VerifyCableSmartCardAction
{
    public function __construct(protected BillProviderInterface $provider) {}

    public function handle(VerifyCableSmartCardDto $dto): string|bool
    {
        $response = $this->provider->verifyCableSmartCard($dto->smart_card_number, $dto->provider_id);
        $deviceName = trim($response->get('customer_name'));

        if (str_contains($deviceName, 'invalid') || empty($deviceName)) {
            return false;
        }

        return $deviceName;
    }
}
