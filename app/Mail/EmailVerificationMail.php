<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\EmailVerification;
use Illuminate\Mail\Mailables\Content;

final class EmailVerificationMail extends CustomMail
{
    public $emailVerification;

    public function __construct(EmailVerification $emailVerification)
    {
        $this->emailVerification = $emailVerification;
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

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.email-verification',
        );
    }
}
