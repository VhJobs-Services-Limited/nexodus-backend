<?php

declare(strict_types=1);

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
