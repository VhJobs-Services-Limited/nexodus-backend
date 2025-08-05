<?php

namespace App\Http\Controllers\Api\V1\Betting;

use App\Actions\Bill\BettingPurchaseAction;
use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\BettingPurchaseDto;
use Illuminate\Http\JsonResponse;

class BettingController
{
    /**
     * Get all
     */
    public function index(BillProviderInterface $bettingProvider): JsonResponse
    {
        return response()->json(['data' => $bettingProvider->getBettingList()]);
    }

    /**
     * Create new
     */
    public function store(BettingPurchaseDto $dto, BettingPurchaseAction $action): JsonResponse
    {
        $response = $action->handle($dto->toArray());

        return response()->json([
            'message' => 'Airtime purchase successful',
            'data' => $response,
        ])->setStatusCode(201);
    }
}
