<?php

declare(strict_types=1);

namespace App\Dtos\User;

use App\Dtos\BaseDto;
use Illuminate\Validation\Rule;

final class UpdateUserDto extends BaseDto
{
    public string $fullname;
    public string $phone_number;
    public ?string $country;

    protected function rules(): array
    {
        return [
            'fullname' => ['sometimes', 'string', 'max:255'],
            'phone_number' => ['sometimes', 'string', "phone:".request('country'), Rule::unique('users', 'phone_number')->ignore(request()->user()?->id)],
            'country' => ['sometimes', 'string', 'max:10'],
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
