<?php

declare(strict_types=1);

namespace App\Dtos\User;

use App\Dtos\BaseDto;
use Illuminate\Validation\Rules\Password;

final class ChangePasswordDto extends BaseDto
{
    public string $old_password;
    public string $new_password;

    public function messages(): array
    {
        return [
            'old_password.current_password' => 'The old password is incorrect',
        ];
    }

    protected function rules(): array
    {
        return [
            'old_password' => ['required', 'current_password:sanctum'],
            'new_password' => ['required', 'confirmed:confirmed_password', Password::min(8)],
            'confirmed_password' => ['required', Password::min(8)],
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
