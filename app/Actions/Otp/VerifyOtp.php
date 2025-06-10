<?php

declare(strict_types=1);

namespace App\Actions\Otp;

use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class VerifyOtp
{
    /**
     * Verify Otp
     */
    public function execute(string $email, int|string $code): void
    {
        $otp = Otp::select('id', 'updated_at', 'code', 'verified_at')
            ->where('email', $email)
            ->whereNull('expired_at')
            ->latest()
            ->firstOrFail();

        throw_if(! Hash::check((string) $code, (string) $otp->code), BadRequestHttpException::class, 'The code provided does not match');

        throw_if((bool) $otp->verified_at, BadRequestHttpException::class, 'The code has already been verified');

        if (now()->diffInMinutes($otp->updated_at, true) > 10) {
            throw_if($otp->update(['expired_at' => now()]), BadRequestHttpException::class, 'The code provided has expired');
        }

        $otp->update(['verified_at' => now()]);
    }
}
