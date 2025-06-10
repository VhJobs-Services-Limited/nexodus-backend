<?php

declare(strict_types=1);

namespace App\Actions\Password;

use App\Actions\Otp\CreateOtp;
use App\Dtos\Password\ForgetPasswordDto;

final class ForgetPassword
{
    public function execute(ForgetPasswordDto $forgetPasswordDto)
    {
        $createOtp = new CreateOtp();
        $createOtp->execute($forgetPasswordDto->email, self::class);
    }
}
