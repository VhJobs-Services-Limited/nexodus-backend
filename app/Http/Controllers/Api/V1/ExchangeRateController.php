<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Crypto\ExchangePriceInterface;
use App\Enums\SettingsEnum;
use App\Models\Setting;

final class ExchangeRateController
{
    public function index(ExchangePriceInterface $exchangePrice)
    {
        $symbols = Setting::where('name', SettingsEnum::SUPPORTED_COINS->value)->first()->value ?? [];

        $data = $exchangePrice->getCachedExchangePrice($symbols);

        return response()->json([
            'data' => $data,
        ]);
    }

    public function show(ExchangePriceInterface $exchangePrice, string $exchange_rate)
    {
        $data = $exchangePrice->getCoinDetail($exchange_rate);

        return response()->json([
            'data' => $data,
        ]);
    }
}
