<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

final class TransactionPinSetMail extends CustomMail
{
    public function __construct(public readonly User $user)
    {
        parent::__construct([]);
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.transaction-pin-set',
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Transaction PIN Set',
        );
    }
}
