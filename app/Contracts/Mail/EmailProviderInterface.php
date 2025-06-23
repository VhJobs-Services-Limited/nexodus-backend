<?php

declare(strict_types=1);

namespace App\Contracts\Mail;

use Illuminate\Support\Collection;
use Symfony\Component\Mime\Email;

interface EmailProviderInterface
{
    public function send(array $payload): Collection;

    public function getProviderName(): string;

    public function buildPayload(Email $email): array;

    public function hasTemplateMail(): bool;
}
