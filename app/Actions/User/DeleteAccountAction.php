<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Dtos\User\DeleteAccountDto;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

final class DeleteAccountAction
{
    public function handle(Authenticatable|User $user, DeleteAccountDto $dto): void
    {
        $user->email = "{$user->email}+trashed";
        $user->phone_number = "{$user->phone_number}+trashed";
        $user->reason_for_deletion = $dto->reason_for_deletion;

        $user->save();

        $user->delete();

        $user->tokens()->delete();
    }
}
