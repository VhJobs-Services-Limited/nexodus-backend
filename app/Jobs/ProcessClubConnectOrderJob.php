<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\StatusEnum;
use App\Models\BillTransaction;
use App\Services\Bill\ClubConnectService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

final class ProcessClubConnectOrderJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Collection $payload) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reference = $this->payload->get('reference');

        if (! $reference) {
            return;
        }

        $billTransaction = BillTransaction::select('id', 'transaction_id', 'amount', 'status', 'payload', 'provider_reference', 'reference', 'last_retried_at')->where('reference', $reference)->first();

        if (! $billTransaction) {
            return;
        }

        if ($billTransaction->status === get_status($this->payload->get('status')) && (bool) $billTransaction->last_retried_at) {
            return;
        }

        $order = app(ClubConnectService::class)->getOrder($billTransaction->provider_reference);

        match ($order->get('status')) {
            'ORDER_COMPLETED' => $billTransaction->update(['status' => StatusEnum::SUCCESS, 'provider_amount' => $order->get('amountcharged') || 0]),
            'ORDER_ONHOLD' => $billTransaction->update(['last_retried_at' => now()]),
            'ORDER_CANCELLED' => (function () use ($billTransaction) {
                $billTransaction->update(['status' => StatusEnum::FAILED]);
                RefundJob::dispatch($billTransaction->transaction);
            })(),
            default => null,
        };
    }
}
