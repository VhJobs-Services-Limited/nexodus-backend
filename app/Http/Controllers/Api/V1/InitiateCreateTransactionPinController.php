<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\TransactionPin\InitiateTransactionPinAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class InitiateCreateTransactionPinController extends Controller
{
    public function __invoke(InitiateTransactionPinAction $initiateTransactionPinAction): JsonResponse
    {

        if ((bool) request()->user()->pin) {
            throw new BadRequestHttpException('Transaction pin already created');
        }

        $initiateTransactionPinAction->handle(request()->user()->email);

        return response()->json(['message' => 'Code has been sent to your email']);
    }
}
