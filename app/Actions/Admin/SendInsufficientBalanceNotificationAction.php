<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Mail\InsufficientBalanceMail;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

final class SendInsufficientBalanceNotificationAction
{
    private const string RATE_LIMIT_KEY = 'insufficient_balance_notification';
    private const int MAX_ATTEMPTS = 1; // Only send 1 notification per hour
    private const int DECAY_MINUTES = 60; // Rate limit window in minutes

    public function handle(float $currentBalance, float $requiredAmount, string $providerName): void
    {
        $rateLimitKey = self::RATE_LIMIT_KEY.':'.$providerName;

        // Check if we've already sent a notification recently
        if (RateLimiter::tooManyAttempts($rateLimitKey, self::MAX_ATTEMPTS)) {
            // Log that we're skipping the notification due to rate limiting
            logger()->info('Skipping insufficient balance notification due to rate limiting', [
                'provider' => $providerName,
                'current_balance' => $currentBalance,
                'required_amount' => $requiredAmount,
                'rate_limit_key' => $rateLimitKey,
            ]);

            return;
        }

        $adminEmail = config('app.admin.email');

        if (empty($adminEmail) || $adminEmail === 'admin@example.com') {
            logger()->warning('Admin email not configured for insufficient balance notifications', [
                'admin_email' => $adminEmail,
                'provider' => $providerName,
            ]);

            return;
        }

        try {
            Mail::to($adminEmail)->queue(new InsufficientBalanceMail(
                currentBalance: $currentBalance,
                requiredAmount: $requiredAmount,
                providerName: $providerName
            ));

            // Increment the rate limiter to track this notification
            RateLimiter::hit($rateLimitKey, self::DECAY_MINUTES * 60);

            logger()->info('Insufficient balance notification sent to admin', [
                'admin_email' => $adminEmail,
                'provider' => $providerName,
                'current_balance' => $currentBalance,
                'required_amount' => $requiredAmount,
            ]);
        } catch (Exception $e) {
            logger()->error('Failed to send insufficient balance notification', [
                'admin_email' => $adminEmail,
                'provider' => $providerName,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
