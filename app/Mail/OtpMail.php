<?php

declare(strict_types=1);

namespace App\Mail;

final class OtpMail extends CustomMail
{
    public function __construct(int $otp)
    {
        parent::__construct([
            'subject' => 'One Time Password',
            'template' => [
                'id' => collect(config('services.sendpulse.templates'))
                    ->where('key', 'otp')
                    ->first()['value'] ?? null,
                'variables' => [
                    'code' => $otp,
                ],
            ],
        ]);
    }
}
