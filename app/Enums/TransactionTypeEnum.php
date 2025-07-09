<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case BILL = 'bill';
    case GIFT_CARD = 'gift card';
    case WITHDRAW = 'withdraw';
    case DEPOSIT = 'deposit';
    case TRANSFER = 'transfer';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
