<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Bill\BillProviderInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataController
{
    /**
     * Get all
     */
    public function index(BillProviderInterface $billProvider): JsonResponse
    {
        return response()->json(['data' => $billProvider->getDataProviders()]);
    }

    /**
     * Create new
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([])->setStatusCode(201);
    }
}
