<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Registration;

use App\Actions\EmailVerification\CreateEmailVerificationAction;
use App\Dtos\EmailVerification\CreateEmailVerificationDto;
use Illuminate\Http\JsonResponse;

final class EmailVerificationController
{
    public function __invoke(
        CreateEmailVerificationDto $dto,
        CreateEmailVerificationAction $action
    ): JsonResponse {
        $verification = $action->handle($dto);

        return response()->json([
            'message' => $verification['message'],
            'data' => $verification['data'],
        ], $verification['code']);
    }
}
