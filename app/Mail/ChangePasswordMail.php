<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;

final class ChangePasswordMail extends CustomMail
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.password-changed',
        );
    }
}
