<?php

declare(strict_types=1);

namespace App\Mail;

use App\Contracts\Mail\EmailProviderInterface;
use App\Mail\Concerns\UsesEmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class CustomMail extends Mailable
{
    use Queueable;
    use SerializesModels;
    use UsesEmailTemplate;

    public function __construct(public array $payload)
    {
    }

    public function build()
    {
        $emailProvider = app(EmailProviderInterface::class);
        $hasTemplateMail = $emailProvider->hasTemplateMail();

        if (!$hasTemplateMail) {
            return $this;
        }

        if (!isset($this->payload['template'])) {
            return $this;
        }

        return $this->html(json_encode($this->payload));
    }
}
