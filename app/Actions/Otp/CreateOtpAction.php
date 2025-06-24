<?php

declare(strict_types=1);

namespace App\Actions\Otp;

use App\Mail\OtpMail;
use App\Models\Otp;
use Closure;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class CreateOtpAction
{
    /**
     * Create new otp
     */
    public function handle(string $email, string $actionClass, ?Closure $callback = null): void
    {
        $rateLimitKey = "otp:$email:$actionClass";

        if (RateLimiter::tooManyAttempts(key: $rateLimitKey, maxAttempts: 2)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            throw new TooManyRequestsHttpException(message: "Too many requests. You may try again in $seconds seconds.");
        }

        $otp = Otp::select(['id', 'code', 'verified_at', 'updated_at'])
            ->where('email', $email)
            ->where('action_class', $actionClass)
            ->whereNull('expired_at')
            ->latest()->first();

        $code = mt_rand(100000, 999999);

        when($otp && $otp?->verified_at !== null && $otp?->expired_at !== null, function () use ($otp, $code) {
            $otp->update(['code' => $code]);
        }, function () use ($code, $email, $actionClass, &$otp) {
            $otp = Otp::create(['code' => $code, 'email' => $email, 'action_class' => $actionClass]);
        });

        is_callable($callback) && $callback($otp);

        RateLimiter::increment(key: $rateLimitKey);

        Mail::to($email)->queue(new OtpMail($code));
    }
}
