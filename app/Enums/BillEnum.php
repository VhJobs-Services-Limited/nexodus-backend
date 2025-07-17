<?php

namespace App\Enums;

enum BillEnum: string
{
    case AIRTIME = 'airtime';
    case DATA = 'data';
    case CABLE = 'cable';
    case ELECTRICITY = 'electricity';
    case WIFI = 'wifi';
    case BETTING = 'betting';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
