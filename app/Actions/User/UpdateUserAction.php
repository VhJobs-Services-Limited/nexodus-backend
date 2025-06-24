<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Dtos\User\UpdateUserDto;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

final class UpdateUserAction
{
    public function handle(Authenticatable|User $user, UpdateUserDto $dto): User
    {
        $user->update($dto->toArray());

        return $user;
    }
}
