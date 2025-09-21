<?php

declare(strict_types=1);

namespace App\Contracts\Crypto;

use Illuminate\Support\Collection;

interface GatewayProviderInterface
{
    public function getCoinDetails(string $symbol): Collection;

    public function processTransaction(array $data): Collection;

    public function processWebhook(string $eventName, Collection $data): bool;
}
