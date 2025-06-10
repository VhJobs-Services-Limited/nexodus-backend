<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;

final class WelcomeMail extends CustomMail
{
    public function __construct(User $user)
    {
        parent::__construct([
            'subject' => 'Welcome to Nexodus',
            'template' => [
                'id' => collect(config('services.sendpulse.templates'))
                    ->where('key', 'welcome')
                    ->first()['value'] ?? null,
                'variables' => [
                    'name' => $user->full_name,
                    'email' => $user->email,
                ],
            ],
        ]);
    }
}
