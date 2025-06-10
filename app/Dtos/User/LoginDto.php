<?php

declare(strict_types=1);

namespace App\Dtos\User;

use App\Dtos\BaseDto;
use Illuminate\Validation\Rules\Password;

final class LoginDto extends BaseDto
{
    public string $email;
    public string $password;
    public ?string $device_id;

    protected function rules(): array
    {

        return [
            'email' => ['required', 'email'],
            'password' => ['required', Password::min(8)],
            'device_token' => ['nullable', 'string'],
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
