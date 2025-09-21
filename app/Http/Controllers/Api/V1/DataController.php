<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Bill\DataPurchaseAction;
use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\DataPurchaseDto;
use Illuminate\Http\JsonResponse;

final class DataController
{
    /**
     * Get all
     */
    public function index(BillProviderInterface $billProvider): JsonResponse
    {
        return response()->json(['data' => $billProvider->getDataList()]);
    }

    /**
     * Create new
     */
    public function store(DataPurchaseDto $dto, DataPurchaseAction $action): JsonResponse
    {
        $response = $action->handle($dto->toArray());

        return response()->json([
            'message' => 'Data purchase successful',
            'data' => $response,
        ])->setStatusCode(201);
    }
}
