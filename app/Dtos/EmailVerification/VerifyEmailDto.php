<?php

declare(strict_types=1);

namespace App\Dtos\EmailVerification;

use App\Dtos\BaseDto;

final class VerifyEmailDto extends BaseDto
{
    public string $email;
    public string $token;

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'token' => ['required', 'string', 'size:6'],
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
