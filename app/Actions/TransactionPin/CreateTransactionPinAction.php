<?php

declare(strict_types=1);

namespace App\Actions\TransactionPin;

use App\Actions\Otp\OtpInterceptorAction;
use App\Dtos\User\CreateTransactionPinDto;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Auth\Authenticatable;

final class CreateTransactionPinAction
{
    /**
     * Create a new class instance.
     */
    public function handle(Authenticatable|User $user, CreateTransactionPinDto $dto)
    {
        $createOtp = new OtpInterceptorAction();

        $createOtp->handle($user->email, InitiateTransactionPinAction::class, function (Otp $otp) use ($user, $dto) {
            $user->update(['pin' => $dto->pin]);
        });
    }
}
