<?php

declare(strict_types=1);
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Str;

if (! function_exists('bcmath')) {
    /**
     * custom bcmath function
     *
     * @param  'add'|'sub'|'mul'|'div'  $operation
     * @param  array<number>  $values
     */
    function bcmath(string $operation, array $values, int $scale = 3): string
    {

        return array_reduce(array_slice($values, 1), fn ($carry, $item) => match ($operation) {
            'add' => bcadd($carry, (string) $item, $scale),
            'sub' => bcsub($carry, (string) $item, $scale),
            'mul' => bcmul($carry, (string) $item, $scale),
            'div' => bcdiv($carry, (string) $item, $scale),
            default => throw new InvalidArgumentException("Invalid operation: $operation"),
        }, (string) $values[0]);
    }
}

if (! function_exists('mysql_error_msg')) {
    function mysql_error_msg($code)
    {
        return match ($code) {
            '2002' => 'Unable to connect to DB',
            '23000' => 'This record already exists or conflicts with another record.',
            '42S02' => 'We are experiencing technical difficulties with our database.',
            '1044' => 'Database access issue detected.',
            '1045' => 'Database authentication issue detected.',
            '22007' => 'invalid date format issue detected',
            default => 'An unexpected database error occurred. Our team has been notified.'
        };
    }
}

if (! function_exists('provider_image_url')) {
    function provider_image_url($code)
    {
        return match (strtolower($code)) {
            'mtn' => asset('images/mtn.png'),
            'glo' => asset('images/glo.png'),
            'airtel' => asset('images/airtel.png'),
            '9mobile' => asset('images/9mobile.png'),
            'startimes' => asset('images/startimes.png'),
            'dstv' => asset('images/dstv.png'),
            'gotv' => asset('images/gotv.png'),
            'showmax' => asset('images/showmax.png'),
            default => throw new InvalidArgumentException("Invalid network code: $code"),
        };
    }
}

if (! function_exists('generate_reference')) {
    function generate_reference(string $prefix = 'REF'): string
    {
        return Str::lower($prefix . '-' . Str::random(10) . '-' . now()->timestamp);
    }
}


if (! function_exists('get_status')) {
    function get_status(string $status)
    {
        return match (str($status)->lower()->toString()) {
            'ongoing', 'pending', 'processing', 'queued', 'otp', 'received', 'new', 'order_onhold','order_received' => PaymentStatusEnum::Pending->value,
            'failed', 'reversed', 'blocked', 'abandoned', 'order_cancelled', 'order_error' => PaymentStatusEnum::Failed->value,
            'success', 'successful', 'order_completed', 'order_processed' => PaymentStatusEnum::Success->value,
        };
    }
}
