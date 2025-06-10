<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\ForgetPassword;

use App\Actions\Password\ResetPassword;
use App\Dtos\Password\ResetPasswordDto;
use Illuminate\Http\JsonResponse;

final class ResetPasswordController
{
    public function __invoke(ResetPasswordDto $dto, ResetPassword $resetPassword): JsonResponse
    {
        $resetPassword->execute($dto);

        return response()->json(['message' => 'Password has been reset successfully']);
    }
}
