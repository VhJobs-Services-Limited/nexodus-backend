<?php

declare(strict_types=1);

namespace App\Dtos\Password;

use App\Dtos\BaseDto;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ResetPasswordDto extends BaseDto
{
    public string $email;
    public string $new_password;

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::exists('users', 'email')],
            'new_password' => ['required', 'confirmed:confirmed_password', Password::min(8)],
            'confirmed_password' => ['required', Password::min(8)],
        ];
    }

    protected function failedValidation(): void
    {
        throw new BadRequestHttpException($this->validator->errors()->first());
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
