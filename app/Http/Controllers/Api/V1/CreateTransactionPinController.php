<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\TransactionPin\CreateTransactionPinAction;
use App\Dtos\User\CreateTransactionPinDto;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CreateTransactionPinController extends Controller
{
    public function __invoke(CreateTransactionPinAction $createTransactionPinAction, CreateTransactionPinDto $dto): JsonResponse
    {
        if (request()->user()->pin) {
            throw new BadRequestHttpException('Transaction pin already created');
        }

        $createTransactionPinAction->handle(request()->user(), $dto);

        return response()->json(['message' => 'Transaction pin created successfully', 'data' => new UserResource(request()->user())]);
    }
}
