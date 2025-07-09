<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;

final class InsufficientBalanceMail extends CustomMail
{
    public function __construct(
        public readonly float $currentBalance,
        public readonly float $requiredAmount,
        public readonly string $providerName
    ) {
        parent::__construct([
            'subject' => 'Insufficient Balance Alert',
            'template' => [
                'id' => collect(config('services.sendpulse.templates'))
                    ->where('key', 'insufficient_balance')
                    ->first()['value'] ?? null,
                'variables' => [
                    'current_balance' => number_format($currentBalance, 2),
                    'required_amount' => number_format($requiredAmount, 2),
                    'provider_name' => $providerName,
                    'shortfall' => number_format($requiredAmount - $currentBalance, 2),
                ],
            ],
        ]);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.insufficient-balance',
        );
    }
}
