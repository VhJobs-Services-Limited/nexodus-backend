<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SettingsEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

final class Setting extends Model
{
    public function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $name = $this->name;

                return match ($name) {
                    SettingsEnum::SUPPORTED_COINS->value => explode(',', $value),
                    SettingsEnum::EXCHANGE_RATE->value => (function () use ($value) {
                        $currency = explode(',', $value);

                        return ['USD' => $currency[0], 'NGN' => $currency[1]];
                    })(),
                    default => $value,
                };
            },
        );
    }
}
