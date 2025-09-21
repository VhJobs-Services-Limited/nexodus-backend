<?php

declare(strict_types=1);

namespace App\Actions\Crypto;

use App\Enums\OperationTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Mail\CryptoSaleCredit;
use App\Models\CryptoTransaction;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

final class CreditAccountAfterCyptoSaleAction
{
    public function handle(Transaction $transaction, CryptoTransaction $cryptoTransaction)
    {
        if ($cryptoTransaction->payment_method === PaymentMethodEnum::Wallet->value) {
            DB::transaction(function () use ($transaction, $cryptoTransaction) {
                $user = $transaction->user;
                $amount = $transaction->amount;

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'reference' => generate_reference(),
                    'status' => StatusEnum::SUCCESS,
                    'transaction_type' => TransactionTypeEnum::DEPOSIT,
                    'description' => "Payment for Crypto purchase with reference {$transaction->reference}",
                ]);

                $transaction->walletTransactions()->create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'balance_before' => $user->wallet_balance,
                    'balance_after' => bcmath('add', [$user->wallet_balance, $amount], 2),
                    'type' => OperationTypeEnum::Credit,
                    'status' => PaymentStatusEnum::Success,
                ]);

                $user->deposit($transaction->amount);

                $cryptoTransaction->update([
                    'transfer_transaction_id' => $transaction->id,
                ]);

                Mail::to($user->email)->send(new CryptoSaleCredit($user->username, $transaction->amount, $cryptoTransaction->payment_method));
            });
        }
    }
}
