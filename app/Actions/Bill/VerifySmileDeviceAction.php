<?php

declare(strict_types=1);

namespace App\Actions\Bill;

use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\VerifySmileDeviceDto;

final class VerifySmileDeviceAction
{
    public function __construct(protected BillProviderInterface $provider) {}

    public function handle(VerifySmileDeviceDto $dto): string|bool
    {
        $response = $this->provider->verifySmileDeviceId($dto->device_id, $dto->provider_id);
        $deviceName = trim($response->get('customer_name'));

        if (str_contains($deviceName, 'invalid') || empty($deviceName)) {
            return false;
        }

        return $deviceName;
    }
}
