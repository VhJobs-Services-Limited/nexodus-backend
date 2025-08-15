<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;

final class LoginNotificationMail extends CustomMail
{
    public $fullname;
    public $ipAddress;
    public $userAgent;
    public $timestamp;

    public function __construct(string $fullname, string $ipAddress, string $userAgent, string $timestamp)
    {
        $this->fullname = $fullname;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->timestamp = $timestamp;
        parent::__construct([
            'subject' => 'New Login Detected - Nexodus',
            'template' => [
                'id' => collect(config('services.sendpulse.templates'))
                    ->where('key', 'login-notification')
                    ->first()['value'] ?? null,
                'variables' => [
                    'name' => $this->fullname,
                    'login_time' => $this->timestamp,
                    'ip_address' => $this->ipAddress,
                    'user_agent' => $this->userAgent,
                    'timestamp' => $this->timestamp,
                ],
            ],
        ]);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.login-notification',
        );
    }
}
