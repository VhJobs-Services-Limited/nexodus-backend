<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Registration;

use App\Actions\EmailVerification\VerifyEmailAction;
use App\Dtos\EmailVerification\VerifyEmailDto;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class VerifyEmailController
{
    public function __invoke(
        VerifyEmailDto $dto,
        VerifyEmailAction $action
    ): JsonResponse {
        $action->handle($dto);

        return response()->json([
            'message' => 'Email verified successfully',
        ], Response::HTTP_OK);
    }
}
