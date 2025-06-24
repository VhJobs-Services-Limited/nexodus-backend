<?php

declare(strict_types=1);

namespace App\Actions\Password;

use App\Actions\Otp\OtpInterceptorAction;
use App\Dtos\Password\ResetPasswordDto;
use App\Models\User;

final class ResetPassword
{
    /**
     * Reset password
     */
    public function execute(ResetPasswordDto $resetPasswordDto): void
    {
        $account = User::select('id')->where('email', $resetPasswordDto->email)->firstOrFail();

        $otpInterceptor = new OtpInterceptorAction();

        $otpInterceptor->execute($resetPasswordDto->email, ForgetPassword::class, function () use ($resetPasswordDto, $account) {
            $account->update(['password' => $resetPasswordDto->new_password]);
        });
    }
}
