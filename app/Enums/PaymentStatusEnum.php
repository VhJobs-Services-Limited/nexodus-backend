<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case Pending = 'pending';
    case Success = 'success';
    case Failed = 'failed';
    case AwaitingApproval = 'awaiting_approval';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
