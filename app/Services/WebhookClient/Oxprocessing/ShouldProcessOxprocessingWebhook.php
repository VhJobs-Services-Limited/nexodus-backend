<?php

declare(strict_types=1);

namespace App\Services\WebhookClient\Oxprocessing;

use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

final class ShouldProcessOxprocessingWebhook implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        return true;
    }
}
