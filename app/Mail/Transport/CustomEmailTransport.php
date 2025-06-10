<?php

declare(strict_types=1);

namespace App\Mail\Transport;

use App\Contracts\Mail\EmailProviderInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

final class CustomEmailTransport extends AbstractTransport
{
    public function __construct(private readonly EmailProviderInterface $emailProvider)
    {
        parent::__construct();
    }

    public function __toString(): string
    {
        return $this->emailProvider->getProviderName();
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $payload = $this->emailProvider->buildPayload($email);

        $this->emailProvider->send($payload);
    }
}
