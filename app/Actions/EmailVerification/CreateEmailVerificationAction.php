<?php

declare(strict_types=1);

namespace App\Actions\EmailVerification;

use App\Dtos\EmailVerification\CreateEmailVerificationDto;
use App\Mail\EmailVerificationMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

final class CreateEmailVerificationAction
{
    public function handle(CreateEmailVerificationDto $dto): array
    {
        $user = User::where('email', $dto->email)->exists();
        $hasVerification = EmailVerification::where('email', $dto->email)->whereNotNull('verified_at')->exists();

        if ($user) {
            return [
                'message' => 'User has already been registered, please login to continue',
                'data' => ['next' => 'login'],
                'code' => 400,
            ];
        }

        if ($hasVerification && ! $user) {
            return [
                'message' => 'Email has already been verified, please register to continue',
                'data' => ['next' => 'register'],
                'code' => 400,
            ];
        }

        $verification = EmailVerification::updateOrCreate([
            'email' => $dto->email,
        ], [
            'token' => (string) rand(100000, 999999),
            'expires_at' => now()->addMinutes(11),
        ]);

        Mail::to($dto->email)->queue(new EmailVerificationMail($verification));

        return [
            'message' => 'Verification email sent successfully',
            'data' => [
                'expires_at' => $verification->expires_at->format('Y-m-d H:i:s'),
                'expires_at_in_minutes' => floor($verification->expires_at->diffInMinutes(null, true)),
            ],
            'code' => 200,
        ];
    }
}
