<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Electricity;

use App\Actions\Bill\VerifyMetreNumberAction;
use App\Dtos\Bill\VerifyMetreNumberDto;

final class VerifyMetreNumberController
{
    public function __invoke(VerifyMetreNumberDto $dto, VerifyMetreNumberAction $action)
    {
        $result = $action->handle($dto);

        return response()->json(['message' => empty($result) ? 'Invalid metre number' : 'Metre number verified', 'data' => $result], $result ? 200 : 400);
    }
}
