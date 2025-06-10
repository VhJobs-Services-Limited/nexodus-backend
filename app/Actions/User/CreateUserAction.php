<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Dtos\User\CreateUserDto;
use App\Mail\WelcomeMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

final class CreateUserAction
{
    public function handle(CreateUserDto $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $verification = EmailVerification::query()->where('email', $dto->email)->firstOrFail();

            $user = User::query()->create(array_merge($dto->toArray(), [
                'email_verified_at' => $verification->verified_at,
            ]));

            $verification->delete();

            Mail::to($user->email)->queue(new WelcomeMail($user));

            return $user;
        });
    }
}
