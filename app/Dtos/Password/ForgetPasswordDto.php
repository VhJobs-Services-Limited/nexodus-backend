<?php

declare(strict_types=1);

namespace App\Dtos\Password;

use App\Dtos\BaseDto;
use Illuminate\Validation\Rule;

final class ForgetPasswordDto extends BaseDto
{
    public string $email;

    public function messages(): array
    {
        return [
            'email.exists' => 'This email is not associated with any account on our system',
        ];
    }

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::exists('users', 'email')],
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
