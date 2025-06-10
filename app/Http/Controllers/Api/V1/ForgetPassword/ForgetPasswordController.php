<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\ForgetPassword;

use App\Actions\Password\ForgetPassword;
use App\Dtos\Password\ForgetPasswordDto;
use Illuminate\Http\JsonResponse;

final class ForgetPasswordController
{
    /**
     * Request for code
     */
    public function __invoke(ForgetPasswordDto $forgetPasswordDto, ForgetPassword $forgetPassword): JsonResponse
    {
        $forgetPassword->execute($forgetPasswordDto);

        return response()->json(['message' => 'Code has been sent to your email']);
    }
}
