<?php

declare(strict_types=1);

namespace App\Mail;

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
        return $this->html(json_encode($this->payload));
    }
}
