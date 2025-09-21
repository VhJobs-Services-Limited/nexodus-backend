<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BillTransaction extends Model
{
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * Get the user that owns the BillTransaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction that owns the BillTransaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn (int|float $value) => $value === 0 ? $value : (float) bcmath('div', [$value, 100], 2),
            set: fn (float|int $value) => $value === 0 ? $value : (int) bcmath('mul', [$value, 100], 0)
        );
    }

    protected function providerAmount(): Attribute
    {
        return Attribute::make(
            get: fn (int|float $value) => $value === 0 ? $value : (float) bcmath('div', [$value, 100], 2),
            set: fn (float|int $value) => $value === 0 ? $value : (int) bcmath('mul', [$value, 100], 0)
        );
    }
}
