<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Contracts\Mail\EmailProviderInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mime\Email;

final class AhaSendService implements EmailProviderInterface
{
    public function getProviderName(): string
    {
        return 'ahasend';
    }

    public function send(array $payload): Collection
    {
        $response = Http::withHeader('X-Api-Key', $this->getAccessToken())
        ->acceptJson()->post(config('services.ahasend.base_url').'/v1/email/send', $payload);

        return $response->collect();
    }

    public function buildPayload(Email $email): array
    {
        $payload = [
            'from' => [
                'name' => config('services.ahasend.sender_name'),
                'email' => config('services.ahasend.sender'),
            ],
            'recipients' => $this->formatRecipients($email),
        ];

        $payload['content']['html_body'] = $email->getHtmlBody();
        $payload['content']['subject'] = $email->getSubject();
        $payload['content']['attachments'] = $this->formatAttachments($email);

        return $payload;
    }

    private function getAccessToken(): ?string
    {
        return config('services.ahasend.api_key');
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
                'data' => base64_encode($attachment->getBody()),
                'base64' => true,
                'file_name' => $attachment->getFilename(),
                'content_type' => $attachment->getContentType(),
            ];
        }

        return $attachments;
    }

    public function hasTemplateMail(): bool
    {
        return false;
    }
}
