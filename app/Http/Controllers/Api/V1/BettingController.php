<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Bill\BillProviderInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function store(Request $request): JsonResponse
    {
        return response()->json([])->setStatusCode(201);
    }
}
