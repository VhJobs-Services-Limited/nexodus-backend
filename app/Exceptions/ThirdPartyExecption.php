<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class ThirdPartyExecption extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        logger($this->getMessage());
    }
}
