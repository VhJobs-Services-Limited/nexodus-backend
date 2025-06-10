<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Registration;

use App\Actions\EmailVerification\CreateEmailVerificationAction;
use App\Dtos\EmailVerification\CreateEmailVerificationDto;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class EmailVerificationController
{
    public function __invoke(
        CreateEmailVerificationDto $dto,
        CreateEmailVerificationAction $action
    ): JsonResponse {
        $verification = $action->handle($dto);

        return response()->json([
            'message' => 'Verification email sent successfully',
            'data' => [
                'expires_at' => $verification->expires_at->format('Y-m-d H:i:s'),
                'expires_at_in_minutes' => floor($verification->expires_at->diffInMinutes(null, true)),
            ],
        ], Response::HTTP_CREATED);
    }
}
