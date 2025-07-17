<?php

namespace App\Enums;

enum StatusEnum: string
{
    case SUCCESS = 'success';
    case PENDING = 'pending';
    case FAILED = 'failed';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
