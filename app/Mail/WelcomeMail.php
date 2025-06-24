<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailables\Content;

final class WelcomeMail extends CustomMail
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        parent::__construct([
            'subject' => 'Welcome to Nexodus',
            'template' => [
                'id' => collect(config('services.sendpulse.templates'))
                    ->where('key', 'welcome')
                    ->first()['value'] ?? null,
                'variables' => [
                    'name' => $user->fullname,
                    'email' => $user->email,
                ],
            ],
        ]);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.welcome',
        );
    }
}
