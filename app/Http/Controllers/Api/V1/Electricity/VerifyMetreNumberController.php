<?php

namespace App\Http\Controllers\Api\V1\Electricity;

use App\Actions\Bill\VerifyMetreNumberAction;
use App\Dtos\Bill\VerifyMetreNumberDto;

class VerifyMetreNumberController
{
    public function __invoke(VerifyMetreNumberDto $dto, VerifyMetreNumberAction $action)
    {
        $result = $action->handle($dto);
        return response()->json(['message' => 'Metre number verification', 'data' => $result]);
    }
}
