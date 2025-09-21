<?php

declare(strict_types=1);

namespace App\Services\Bill;

use App\Actions\Admin\SendInsufficientBalanceNotificationAction;
use App\Contracts\Bill\BillProviderInterface;
use Exception;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

abstract class AbstractProvider implements BillProviderInterface
{
    /**
     * {@inheritDoc}
     */
    final public function hasEnoughBalance(float|int $amount): bool
    {
        $currentBalance = $this->getWalletBalance();
        $hasEnoughBalance = $currentBalance >= $amount;

        if (! $hasEnoughBalance) {
            app(SendInsufficientBalanceNotificationAction::class)->handle(
                currentBalance: $currentBalance,
                requiredAmount: $amount,
                providerName: self::getProviderName()
            );

            throw new ServiceUnavailableHttpException(503, 'Insufficient balance');
        }

        return $hasEnoughBalance;
    }

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
