<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Contracts\Mail\EmailProviderInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mime\Email;

final class SendpulseService implements EmailProviderInterface
{
    private const CACHE_KEY = 'sendpulse_access_token';
    private const CACHE_TTL = 60 * 60 - 2;

    public function getProviderName(): string
    {
        return 'sendpulse';
    }

    public function send(array $payload): Collection
    {
        $response = Http::withToken(
            $this->getAccessToken()
        )->acceptJson()->post(config('services.sendpulse.base_url').'/smtp/emails', ['email' => $payload]);

        logger('Response:', [$response->collect()]);

        return $response->collect();
    }

    public function buildPayload(Email $email): array
    {
        $payload = [
            'subject' => $email->getSubject(),
            'from' => [
                'name' => config('services.sendpulse.sender_name'),
                'email' => config('services.sendpulse.sender'),
            ],
            'to' => $this->formatRecipients($email),
            'attachments' => $this->formatAttachments($email),
        ];

        if (! json_decode($email->getHtmlBody(), true)) {
            $payload['html'] = base64_encode($email->getHtmlBody());
        } else {
            $payload = array_merge($payload, json_decode($email->getHtmlBody(), true));
        }

        return $payload;
    }

    private function getAccessToken(): ?string
    {
        return cache()->remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $response = Http::acceptJson()->post(config('services.sendpulse.base_url').'/oauth/access_token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.sendpulse.client_id'),
                'client_secret' => config('services.sendpulse.secret'),
            ]);

            return $response->collect()->get('access_token', null);
        });
    }

    private function formatRecipients(Email $email): array
    {
        $recipients = [];
        foreach ($email->getTo() as $address) {
            $recipients[] = [
                'email' => $address->getAddress(),
                'name' => $address->getName(),
            ];
        }

        return $recipients;
    }

    private function formatAttachments(Email $email): array
    {
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = [
                'content' => base64_encode($attachment->getBody()),
                'filename' => $attachment->getFilename(),
                'type' => $attachment->getContentType(),
            ];
        }

        return $attachments;
    }

    public function hasTemplateMail(): bool
    {
        return true;
    }
}
