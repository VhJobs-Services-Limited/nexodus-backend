<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

final class OtpMail extends CustomMail
{
    public $otp;

    public function __construct(int $otp)
    {
        $this->otp = $otp;
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

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.otp',
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'One Time Password',
        );
    }
}
