<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\Crypto\CreditAccountAfterCyptoSaleAction;
use App\Models\CryptoTransaction;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class CreditAccountAfterCyptoSaleJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Transaction $transaction, public CryptoTransaction $cryptoTransaction)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CreditAccountAfterCyptoSaleAction $creditAccountAfterCyptoSaleAction): void
    {
        $creditAccountAfterCyptoSaleAction->handle($this->transaction, $this->cryptoTransaction);
    }
}
