<?php

declare(strict_types=1);

namespace App\Actions\Password;

use App\Actions\Otp\CreateOtpAction;
use App\Dtos\Password\ForgetPasswordDto;

final class ForgetPassword
{
    public function execute(ForgetPasswordDto $forgetPasswordDto)
    {
        $createOtp = new CreateOtpAction();
        $createOtp->handle($forgetPasswordDto->email, self::class);
    }
}
