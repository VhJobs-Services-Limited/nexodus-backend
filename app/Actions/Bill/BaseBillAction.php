<?php

declare(strict_types=1);

namespace App\Actions\Bill;

use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\BaseBillDto;
use App\Enums\OperationTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\BillTransaction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

abstract class BaseBillAction
{
    public function __construct(protected BillProviderInterface $provider) {}

    abstract public function handle(array $dto): BillTransaction;

    protected function createBillTransaction(BaseBillDto $dto): BillTransaction
    {
        $user = User::find(request()->user()->id);

        $user->throwExceptionIfFundIsInsufficient($dto->amount);

        return DB::transaction(function () use ($user, $dto) {
            $user->withdraw($dto->amount);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $dto->amount,
                'reference' => generate_reference(),
                'status' => StatusEnum::SUCCESS,
                'transaction_type' => TransactionTypeEnum::BILL,
                'description' => $dto->description ?? 'Bill purchase',
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
                'payload' => $dto->payload,
                'type' => $dto->type,
                'provider_name' => $this->provider->getProviderName(),
            ]);

            return $billTransaction;
        });
    }
}
