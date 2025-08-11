<?php

namespace App\Http\Controllers\Api\V1\Cable;

use App\Actions\Bill\CablePurchaseAction;
use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\CablePurchaseDto;
use Illuminate\Http\JsonResponse;

class CableController
{
    /**
     * Get all
     */
    public function index(BillProviderInterface $billProvider): JsonResponse
    {
        return response()->json(['data' => $billProvider->getCableList()]);
    }

    /**
     * Create new
     */
    public function store(CablePurchaseDto $dto, CablePurchaseAction $action): JsonResponse
    {
        $response = $action->handle($dto->toArray());

        return response()->json([
            'message' => 'Cable purchase successful',
            'data' => $response,
        ])->setStatusCode(201);
    }
}
