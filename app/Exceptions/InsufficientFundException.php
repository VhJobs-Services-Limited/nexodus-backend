<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class InsufficientFundException extends Exception
{
    protected $message = 'insufficient fund';
}
