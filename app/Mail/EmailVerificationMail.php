<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\EmailVerification;

final class EmailVerificationMail extends CustomMail
{
    public function __construct(EmailVerification $emailVerification)
    {
        parent::__construct([
            'subject' => 'Verify Your Email Address',
            'template' => [
                'id' => collect(config('services.sendpulse.templates'))
                    ->where('key', 'otp')
                    ->first()['value'] ?? null,
                'variables' => [
                    'code' => $emailVerification->token,
                ],
            ],
        ]);
    }
}
