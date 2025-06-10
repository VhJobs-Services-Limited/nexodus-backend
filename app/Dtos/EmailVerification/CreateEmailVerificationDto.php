<?php

declare(strict_types=1);

namespace App\Dtos\EmailVerification;

use App\Dtos\BaseDto;

final class CreateEmailVerificationDto extends BaseDto
{
    public string $email;

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
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
