<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\CryptoTransaction;
use App\Models\Transaction;
use Illuminate\Mail\Mailables\Content;

final class CryptoSaleMail extends CustomMail
{
    public function __construct(
        public readonly string $username,
        public readonly Transaction $transaction,
        public readonly CryptoTransaction $cryptoTransaction
    ) {
        parent::__construct([
        ]);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.crypto-sale',
        );
    }
}
