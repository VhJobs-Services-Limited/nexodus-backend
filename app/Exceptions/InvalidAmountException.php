<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class InvalidAmountException extends Exception
{
    protected $message = 'invalid amount';
}
