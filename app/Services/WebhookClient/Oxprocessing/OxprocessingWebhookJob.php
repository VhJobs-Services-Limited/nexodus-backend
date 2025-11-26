<?php

declare(strict_types=1);

namespace App\Services\WebhookClient\Oxprocessing;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\Services\Crypto\OxProcessingService;
use Exception;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

final class OxprocessingWebhookJob extends ProcessWebhookJob
{
    public function handle(OxProcessingService $oxProcessingService)
    {
        $payload = $this->webhookCall?->payload;

        // find the transaction
        $transaction = Transaction::where('reference', $payload['BillingID'])->firstOrFail();

        if ($transaction->status === get_status($payload['Status'])) {
            return;
        }

        logger('Oxprocessing with incoming webhook with reference: '.$payload['BillingID'].' has an '.$payload['Status'].' transaction type');

        logger('Transaction type: '.$transaction->transaction_type.get_status($payload['Status']));

        match ($transaction->transaction_type) {
            TransactionTypeEnum::CRYPTO->value => $oxProcessingService->processWebhook(TransactionTypeEnum::CRYPTO->value, collect($payload)),
            default => throw new Exception('Oxprocessing with incoming webhook with reference: '.$payload['BillingID'].' has an invalid transaction type')
        };
    }
}
