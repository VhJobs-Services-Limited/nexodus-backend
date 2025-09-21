<?php

declare(strict_types=1);

namespace App\Enums;

enum SettingsEnum: string
{
    case EXCHANGE_RATE = 'exchange rate';
    case SUPPORTED_COINS = 'supported coins';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
