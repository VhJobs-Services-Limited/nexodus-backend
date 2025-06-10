<?php

declare(strict_types=1);

namespace App\Dtos\User;

use App\Dtos\BaseDto;
use Illuminate\Validation\Rule;

final class CreateUserDto extends BaseDto
{
    public string $fullname;
    public string $username;
    public string $phone_number;
    public string $email;
    public string $password;
    public ?string $country;

    protected function rules(): array
    {
        return [
            'fullname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'phone_number' => ['required', 'string', "phone:".request('country'), 'unique:users,phone_number'],
            'email' => ['required', 'email', 'unique:users,email', Rule::exists('email_verifications', 'email')->whereNotNull('verified_at')],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'country' => ['required', 'string', 'max:10'],
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
