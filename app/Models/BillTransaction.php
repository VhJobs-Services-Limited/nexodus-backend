<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillTransaction extends Model
{
    protected $casts = [
        'payload' => 'array',
    ];

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn (int|float $value) => (float) bcmath('div', [$value, 100], 2),
            set: fn (float|int $value) => (int) bcmath('mul', [$value, 100], 0)
        );
    }
    protected function providerAmount(): Attribute
    {
        return Attribute::make(
            get: fn (int|float $value) => (float) bcmath('div', [$value, 100], 2),
            set: fn (float|int $value) => (int) bcmath('mul', [$value, 100], 0)
        );
    }
    /**
     * Get the user that owns the BillTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction that owns the BillTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
