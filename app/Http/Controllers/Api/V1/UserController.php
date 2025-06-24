<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Dtos\User\CreateUserDto;
use App\Dtos\User\UpdateUserDto;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $user = $action->handle($dto);

        return response()->json([
            'message' => 'User created successfully',
            'data' => [
                'token' => $user->createToken($user->email)->plainTextToken,
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
}
