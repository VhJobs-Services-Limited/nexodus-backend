<?php

declare(strict_types=1);

namespace App\Actions\EmailVerification;

use App\Dtos\EmailVerification\VerifyEmailDto;
use App\Models\EmailVerification;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class VerifyEmailAction
{
    public function handle(VerifyEmailDto $dto): void
    {
        $verification = EmailVerification::query()
            ->where('email', $dto->email)
            ->where('token', $dto->token)
            ->first();

        if (! $verification) {
            throw new BadRequestHttpException('Invalid verification code');
        }

        if ($verification->expires_at->isPast()) {
            throw new BadRequestHttpException('Verification code has expired');
        }

        if ($verification->verified_at !== null) {
            throw new BadRequestHttpException('Email has already been verified');
        }

        $verification->update(['verified_at' => now()]);
    }
}
