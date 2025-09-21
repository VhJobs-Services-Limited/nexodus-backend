<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case Wallet = 'wallet';
    case Bank = 'bank';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
