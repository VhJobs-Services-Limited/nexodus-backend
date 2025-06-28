<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Dtos\User\LoginDto;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

final class LoginController extends Controller
{
    public function __invoke(LoginDto $dto): JsonResponse
    {
        $user = User::where('email', $dto->email)->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            return response()->json(['message' => 'The provided credentials do not match our records.'], 400);
        }

        when($dto->device_token !== null, fn () => $user->update(['device_token' => $dto->device_token]));

        return response()->json(['data' => [
            'token' => $user->createToken($dto->email)->plainTextToken,
            'user' => UserResource::make($user),
        ]]);
    }
}
