<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Otp\VerifyOtp;
use App\Dtos\VerifyOtpDto;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class VerifyOtpController extends Controller
{
    public function __invoke(VerifyOtpDto $dto, VerifyOtp $verifyOtp)
    {
        $email = $dto->email;

        if (! $email && request()->bearerToken() && $user = Auth::guard('sanctum')->user()) {
            $email = $user->email;
        }

        throw_if(
            ! $email,
            BadRequestHttpException::class,
            'Email is required when request is from a guest user'
        );

        $verifyOtp->execute($email, $dto->code);

        return response()->json(['message' => 'Code has been verified successfully']);
    }
}
