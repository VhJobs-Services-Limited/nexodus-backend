<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Bill\AirtimePurchaseAction;
use App\Contracts\Bill\BillProviderInterface;
use App\DTOs\Bill\AirtimePurchaseDto;
use Illuminate\Http\JsonResponse;

class AirtimeController
{
    /**
     * Get all
     */
    public function index(BillProviderInterface $billProvider): JsonResponse
    {
        return response()->json(['data' => $billProvider->getAirtimeList()]);
    }

    /**
     * Create new
     */
    public function store(AirtimePurchaseDto $dto, AirtimePurchaseAction $action): JsonResponse
    {
        $response = $action->handle($dto->toArray());

        return response()->json([
            'message' => 'Airtime purchase successful',
            'data' => $response,
        ])->setStatusCode(201);
    }
}
