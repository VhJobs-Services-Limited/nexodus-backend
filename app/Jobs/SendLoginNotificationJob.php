<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\LoginNotificationMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

final class SendLoginNotificationJob implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public User $user, public string $ipAddress, public string $userAgent, public string $timestamp) {}

    public function handle(): void
    {
        Mail::to($this->user->email)->send(new LoginNotificationMail($this->user->fullname, $this->ipAddress, $this->userAgent, $this->timestamp));
    }
}
