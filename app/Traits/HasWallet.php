<?php

declare(strict_types=1);

namespace App\Traits;

use App\Exceptions\InsufficientFundException;
use App\Exceptions\InvalidAmountException;
use Exception;
use Illuminate\Support\Facades\DB;

trait HasWallet
{
    public function deposit(int|float $amount): void
    {
        if (! DB::transactionLevel()) {
            throw new Exception('This method should be called within a transaction');
        }

        $this->throwExceptionIfAmountIsInvalid($amount);

        $user = $this->lockForUpdate()->where('id', $this->id)->first();

        $user->increment('wallet_balance', (int) bcmath('mul', [$amount, 100], 0));
    }

    public function withdraw(int|float $amount): void
    {
        if (! DB::transactionLevel()) {
            throw new Exception('This method should be called within a transaction');
        }

        $this->throwExceptionIfAmountIsInvalid($amount);

        $user = $this->lockForUpdate()->where('id', $this->id)->first();

        $this->throwExceptionIfFundIsInsufficient($amount);

        $user->decrement('wallet_balance', (int) bcmath('mul', [$amount, 100], 0));
    }

    public function canWithdraw(int|float $amount): bool
    {

        $this->throwExceptionIfAmountIsInvalid($amount);

        $balance = $this->wallet_balance ?? 0;

        return $balance >= $amount;
    }

    public function throwExceptionIfAmountIsInvalid(int|float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidAmountException();
        }
    }

    public function throwExceptionIfFundIsInsufficient(int|float $amount): void
    {
        if (! $this->canWithdraw($amount)) {
            throw new InsufficientFundException();
        }
    }
}
