<?php

namespace App\Http\Controllers\Api\V1\Betting;

use App\Actions\Bill\VerifyBettingAccountAction;
use App\Dtos\Bill\VerifyBettingAccountDto;

class VerifyBettingAccountController
{
    public function __invoke(VerifyBettingAccountDto $dto, VerifyBettingAccountAction $action)
    {
        $result = $action->handle($dto);
        return response()->json(['message' => empty($result) ? 'Invalid betting account id' : 'Betting account verified', 'data' => $result], $result ? 200 : 400);
    }
}
