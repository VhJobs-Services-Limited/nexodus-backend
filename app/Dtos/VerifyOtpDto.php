<?php

declare(strict_types=1);

namespace App\Dtos;

use Illuminate\Validation\Rule;

final class VerifyOtpDto extends BaseDto
{
    public ?string $email;
    public int|string $code;

    public function messages(): array
    {
        return [
            'email.exists' => 'This email has not been sent a code',
        ];
    }

    protected function rules(): array
    {
        return [
            'email' => ['nullable', 'email', Rule::exists('otps', 'email')],
            'code' => ['required', 'integer', 'digits:6'],
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
