<?php

declare(strict_types=1);

namespace App\Contracts\Mail;

use Symfony\Component\Mime\Email;

interface EmailTransportInterface
{
    public function buildPayload(Email $email): array;

    public function formatRecipients(Email $email): array;

    public function formatAttachments(Email $email): array;

    public function getAccessToken(): string;
}
