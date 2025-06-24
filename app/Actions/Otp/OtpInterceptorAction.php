<?php

declare(strict_types=1);

namespace App\Actions\Otp;

use App\Models\Otp;
use Closure;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class OtpInterceptorAction
{
    /**
     * Otp interceptor
     */
    public function handle(string $email, string $actionClass, ?Closure $callback = null): void
    {
        $otp = Otp::select('id', 'updated_at', 'code', 'verified_at')
            ->where('email', $email)
            ->where('action_class', $actionClass)
            ->whereNull('expired_at')
            ->latest()
            ->first();

        throw_if(! $otp, BadRequestHttpException::class, 'The code has either passed the usage timeout or does not exist - Please generate a new code');

        throw_if(! $otp->verified_at, BadRequestHttpException::class, 'The code has not been verified');

        if (now()->diffInMinutes($otp->verified_at, true) > 10) {
            throw_if($otp->update(['expired_at' => now()]), BadRequestHttpException::class, 'The code provided has expired');
        }

        $otp->update(['expired_at' => now()]);

        is_callable($callback) && $callback($otp);
    }
}
