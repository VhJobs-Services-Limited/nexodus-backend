<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Dtos\User\CreateUserDto;
use App\Dtos\User\UpdateUserDto;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UserController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => UserResource::make($request->user()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserDto $dto, CreateUserAction $action): JsonResponse
    {
        // check if username already exists
        $exists = User::where('username', Str::lower($dto->username))->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Username is already chosen, try these:',
                'data' => $this->generateSuggestions($dto->username),
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $action->handle($dto);

        return response()->json([
            'message' => 'User created successfully',
            'data' => [
                'token' => $user->createToken($user->email)->plainTextToken,
                'user' => UserResource::make($user),
            ],
        ], Response::HTTP_CREATED);
    }

    public function update(UpdateUserDto $dto, UpdateUserAction $action): JsonResponse
    {
        return response()->json([
            'message' => 'User updated successfully',
            'data' => UserResource::make($action->handle(request()->user(), $dto)),
        ]);
    }

    private function generateSuggestions(string $username): array
    {
        if (! preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new BadRequestHttpException('Username can only contain letters, numbers, and underscores');
        }

        $baseUsername = Str::lower($username);

        $words = explode('_', $baseUsername);
        $firstWord = $words[0] ?? $baseUsername;

        // Generate all possible suggestions first
        $possibleSuggestions = collect([
            $baseUsername,
            // Simple number combinations
            ...collect(range(1, 2))->map(fn ($num) => mb_strtolower("$baseUsername$num")),
            // Creative combinations
            mb_strtolower("{$firstWord}_official"),
            mb_strtolower("{$firstWord}_verified"),
            mb_strtolower("{$firstWord}_pro"),
            mb_strtolower("real_{$firstWord}"),
            mb_strtolower("the_{$firstWord}"),
            // Underscore combinations
            ...collect(range(1, 2))->map(fn ($num) => mb_strtolower("{$firstWord}_{$num}")),
            ...collect(range(1, 2))->map(fn ($num) => mb_strtolower("{$firstWord}_user_{$num}")),
            // Random string combinations
            ...collect(range(1, 2))->map(fn () => mb_strtolower("{$firstWord}_".Str::random(4))),
            ...collect(range(1, 2))->map(fn () => mb_strtolower(Str::random(4))."_{$firstWord}"),
        ]);

        $existingUsernames = User::whereIn('username', $possibleSuggestions)
            ->pluck('username')
            ->toArray();

        return $possibleSuggestions
            ->reject(fn ($suggestion) => in_array($suggestion, $existingUsernames))
            ->random(5)
            ->values()
            ->toArray();
    }
}
