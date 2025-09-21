<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Wifi;

use App\Actions\Bill\DataPurchaseAction;
use App\Contracts\Bill\BillProviderInterface;
use App\Dtos\Bill\WifiPurchaseDto;
use Illuminate\Http\JsonResponse;

final class WifiController
{
    /**
     * Get all
     */
    public function index(BillProviderInterface $billProvider): JsonResponse
    {
        return response()->json(['data' => $billProvider->getWifiList()->toArray()]);
    }

    /**
     * Create new
     */
    public function store(WifiPurchaseDto $dto, DataPurchaseAction $action): JsonResponse
    {
        $response = $action->handle($dto->toArray());

        return response()->json([
            'message' => 'Wifi purchase successful',
            'data' => $response,
        ])->setStatusCode(201);
    }
}
