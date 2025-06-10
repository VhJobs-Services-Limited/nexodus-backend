<?php

declare(strict_types=1);

namespace App\Actions\EmailVerification;

use App\Dtos\EmailVerification\CreateEmailVerificationDto;
use App\Mail\EmailVerificationMail;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Mail;

final class CreateEmailVerificationAction
{
    public function handle(CreateEmailVerificationDto $dto): EmailVerification
    {
        $verification = EmailVerification::updateOrCreate([
            'email' => $dto->email,
        ], [
            'token' => (string) rand(100000, 999999),
            'expires_at' => now()->addMinutes(11),
        ]);

        Mail::to($dto->email)->queue(new EmailVerificationMail($verification));

        return $verification;
    }
}
