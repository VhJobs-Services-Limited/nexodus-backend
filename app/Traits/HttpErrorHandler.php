<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait HttpErrorHandler
{
    protected function errorHandler(Response $response)
    {
        $status = $response->status();
        $notFoundError = [404];
        $clientErrors = range(400, 499);
        $serverErrors = range(500, 599);

        return match (true) {
            in_array($status, $notFoundError) => throw new NotFoundHttpException($response->collect()->get('message') ?? 'The provider resource was not found'),
            in_array($status, $clientErrors) => throw new BadRequestHttpException($response->collect()->get('message') ?? 'A client error has occurred from the provider'),
            in_array($status, $serverErrors) => throw new Exception($response->collect()->get('message') ?? 'A server error has occurred from the provider'),
            default => null,
        };
    }
}
