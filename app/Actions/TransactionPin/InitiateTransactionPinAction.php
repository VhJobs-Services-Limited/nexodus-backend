<?php

declare(strict_types=1);

namespace App\Actions\TransactionPin;

use App\Actions\Otp\CreateOtpAction;

final class InitiateTransactionPinAction
{
    /**
     * Create a new class instance.
     */
    public function handle(string $email)
    {
        (new CreateOtpAction())->handle($email, self::class);
    }
}
