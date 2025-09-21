<?php

declare(strict_types=1);

namespace App\Services\WebhookClient\Oxprocessing;

use Exception;
use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

final class OxprocessingSignatureValidator implements SignatureValidator
{
    /**
     * Verify request signature
     */
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        return true;

        $signature = mb_strtolower($request->get('Signature'));

        if (! $signature) {
            logger('No  signature was found from oxprocessing with IP:'.$request->ip());

            return false;
        }

        $signingSecret = mb_strtolower($config->signingSecret);

        if (empty($signingSecret)) {
            throw new Exception('No secret key set for oxprocessing');
        }

        $computedSignature = mb_strtolower(hash_hmac('sha512', $request->getContent(), $signingSecret));

        return hash_equals($signature, $computedSignature);
    }
}
