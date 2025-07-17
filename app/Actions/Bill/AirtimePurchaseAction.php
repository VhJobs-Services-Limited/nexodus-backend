<?php

namespace App\Actions\Bill;

use App\Contracts\Bill\BillProviderInterface;
use App\DTOs\Bill\AirtimePurchaseDto;
use App\Enums\BillEnum;
use App\Enums\OperationTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\ThirdPartyExecption;
use App\Jobs\RefundJob;
use App\Models\BillTransaction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AirtimePurchaseAction
{
    public function __construct(
        private readonly BillProviderInterface $provider,
    ) {
    }

    /**
     * Create a new class instance.
     */
    public function handle(AirtimePurchaseDto $dto): BillTransaction
    {
        $user = User::find(request()->user()->id);

        $user->throwExceptionIfFundIsInsufficient($dto->amount);

        $billTransaction = DB::transaction(function () use ($user, $dto) {
            $user->withdraw($dto->amount);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $dto->amount,
                'reference' => generate_reference(),
                'status' => StatusEnum::SUCCESS,
                'transaction_type' => TransactionTypeEnum::BILL,
                'description' => 'Airtime purchase',
            ]);

            $transaction->walletTransactions()->create([
                'user_id' => $user->id,
                'amount' => $dto->amount,
                'balance_before' => $user->wallet_balance,
                'balance_after' => bcmath('sub', [$user->wallet_balance, $dto->amount], 0),
                'type' => OperationTypeEnum::Debit,
                'status' => PaymentStatusEnum::Success,
            ]);

            $billTransaction = $transaction->billTransactions()->create([
                'user_id' => $user->id,
                'amount' => $dto->amount,
                'reference' => generate_reference(),
                'status' => StatusEnum::PENDING,
                'payload' => $dto->toArray(),
                'type' => BillEnum::AIRTIME,
            ]);

            return $billTransaction;
        });

        $response = $this->provider->purchaseAirtime($billTransaction);

        if (!$response->has('orderid')) {
            RefundJob::dispatch($billTransaction->transaction);
            throw new ThirdPartyExecption($response->get('status', 'We are unable to process your request at the moment, please try again later'));
        }

        $billTransaction->update(['provider_reference' => $response->get('orderid'), 'provider_name' => $this->provider->getProviderName()]);

        return $billTransaction;
    }
}
