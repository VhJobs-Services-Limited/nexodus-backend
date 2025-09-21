<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Electricity;

use App\Actions\Bill\ElectricityPurchaseAction;
use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\ElectricityPurchaseDto;
use Illuminate\Http\JsonResponse;

final class ElectricityController
{
    /**
     * Get all
     */
    public function index(BillProviderInterface $billProvider): JsonResponse
    {
        return response()->json(['data' => $billProvider->getElectricityList()]);
    }

    /**
     * Create new
     */
    public function store(ElectricityPurchaseDto $dto, ElectricityPurchaseAction $action): JsonResponse
    {
        $response = $action->handle($dto->toArray());

        return response()->json([
            'message' => 'Electricity purchase successful',
            'data' => $response,
        ])->setStatusCode(201);
    }
}
