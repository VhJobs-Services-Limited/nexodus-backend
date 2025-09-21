<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\QueryScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Transaction extends Model
{
    use HasFactory;
    use QueryScope;

    /**
     * Get the user that owns the Transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet transactions for the Transaction
     */
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get the bill transactions for the Transaction
     */
    public function billTransactions(): HasMany
    {
        return $this->hasMany(BillTransaction::class);
    }

    /**
     * Get the bill transaction for bill type transactions
     */
    public function billTransaction(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BillTransaction::class);
    }

    /**
     * Get the wallet transaction for deposit, withdraw, and transfer type transactions
     */
    public function walletTransaction(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WalletTransaction::class);
    }

    /**
     * Scope to get bill type transactions
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBillType($query)
    {
        return $query->where('transaction_type', \App\Enums\TransactionTypeEnum::BILL);
    }

    /**
     * Scope to get wallet type transactions (deposit, withdraw, transfer)
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeWalletType($query)
    {
        return $query->whereIn('transaction_type', [
            \App\Enums\TransactionTypeEnum::DEPOSIT,
            \App\Enums\TransactionTypeEnum::WITHDRAW,
            \App\Enums\TransactionTypeEnum::TRANSFER,
        ]);
    }

    /**
     * Scope to filter transactions by amount range
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAmountRange($query, array $amounts)
    {
        if (isset($amounts['min'])) {
            $query->where('amount', '>=', $amounts['min']);
        }

        if (isset($amounts['max'])) {
            $query->where('amount', '<=', $amounts['max']);
        }

        return $query;
    }

    public function cryptoTransactions(): HasMany
    {
        return $this->hasMany(CryptoTransaction::class);
    }

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn (int|float $value) => $value === 0 ? $value : (float) bcmath('div', [$value, 100], 2),
            set: fn (float|int $value) => $value === 0 ? $value : (int) bcmath('mul', [$value, 100], 0)
        );
    }
}
