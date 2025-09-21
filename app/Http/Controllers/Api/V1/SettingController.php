<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SettingController
{
    /**
     * Get all
     */
    public function index()
    {
        return response()->json([
            'message' => 'Settings fetched successfully',
            'data' => Setting::all(),
        ]);
    }

    /**
     * Create new
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([])->setStatusCode(201);
    }

    /**
     * Update by id
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json();
    }
}
