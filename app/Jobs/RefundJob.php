<?php

namespace App\Jobs;

use App\Enums\OperationTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class RefundJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Transaction $transaction)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->transaction->user;
        $amount = $this->transaction->amount;


        DB::transaction(function () use ($user, $amount) {
            $user->deposit($amount);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'reference' => generate_reference(),
                'status' => StatusEnum::SUCCESS,
                'transaction_type' => TransactionTypeEnum::BILL,
                'description' => 'Airtime purchase refund',
            ]);

            $transaction->walletTransactions()->create([
                'user_id' => $user->id,
                'amount' => $amount,
                'balance_before' => $user->wallet_balance,
                'balance_after' => bcmath('sub', [$user->wallet_balance, $amount], 0),
                'type' => OperationTypeEnum::Credit,
                'status' => PaymentStatusEnum::Success,
            ]);

            $this->transaction->billTransactions()->update(['status' => StatusEnum::FAILED]);
        });
    }
}
