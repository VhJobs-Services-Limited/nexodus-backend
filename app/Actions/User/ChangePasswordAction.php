<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Dtos\User\ChangePasswordDto;
use App\Mail\ChangePasswordMail;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Mail;

final class ChangePasswordAction
{
    public function handle(User|Authenticatable $user, ChangePasswordDto $dto)
    {
        $user->update(['password' => $dto->new_password]);

        Mail::to($user->email)->queue(new ChangePasswordMail());

        return $user;
    }
}
