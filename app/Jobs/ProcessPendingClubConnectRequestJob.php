<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\StatusEnum;
use App\Models\BillTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class ProcessPendingClubConnectRequestJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        BillTransaction::select('id', 'transaction_id', 'amount', 'status', 'payload', 'provider_reference', 'reference')->where('status', StatusEnum::PENDING)->whereNotNull('last_retried_at')
            ->where('last_retried_at', '<=', now())
            ->where('created_at', '>=', now()->subHour())->each(function ($billTransaction) {
                ProcessClubConnectOrderJob::dispatch(collect(['reference' => $billTransaction->reference, 'status' => $billTransaction->status]));
            });
    }
}
