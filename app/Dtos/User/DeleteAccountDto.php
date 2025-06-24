<?php

declare(strict_types=1);

namespace App\Dtos\User;

use App\Dtos\BaseDto;

final class DeleteAccountDto extends BaseDto
{
    public string $reason_for_deletion;

    protected function rules(): array
    {
        return [
            'reason_for_deletion' => ['required', 'string', 'max:255'],
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
