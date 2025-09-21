<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use App\Contracts\Crypto\GatewayProviderInterface;
use App\Traits\HttpErrorHandler;

abstract class AbstractProvider implements GatewayProviderInterface
{
    use HttpErrorHandler;
}
