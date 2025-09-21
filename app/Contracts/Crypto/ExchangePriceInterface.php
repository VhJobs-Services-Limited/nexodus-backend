<?php

declare(strict_types=1);

namespace App\Contracts\Crypto;

use Illuminate\Support\Collection;

interface ExchangePriceInterface
{
    public function getExchangePrice(array $symbols = []): Collection;

    public function getCachedExchangePrice(array $symbols = []): Collection;

    public function getCoinDetail(string $string): Collection;

    public function getCachedCoinLists(): Collection;
}
