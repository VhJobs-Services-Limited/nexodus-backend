<?php

declare(strict_types=1);

namespace App\Enums;

enum OperationTypeEnum: string
{
    case Credit = 'credit';
    case Debit = 'debit';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
