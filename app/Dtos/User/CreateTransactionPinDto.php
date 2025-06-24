<?php

declare(strict_types=1);

namespace App\Dtos\User;

use App\Dtos\BaseDto;

final class CreateTransactionPinDto extends BaseDto
{
    public string $pin;

    protected function rules(): array
    {
        return [
            'pin' => ['required', 'string', 'min:4', 'max:4', 'confirmed'],
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }
}
