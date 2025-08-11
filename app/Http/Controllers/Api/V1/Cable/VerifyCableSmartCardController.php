<?php

namespace App\Http\Controllers\Api\V1\Cable;

use App\Actions\Bill\VerifyCableSmartCardAction;
use App\Dtos\Bill\VerifyCableSmartCardDto;

class VerifyCableSmartCardController
{
    public function __invoke(VerifyCableSmartCardDto $dto, VerifyCableSmartCardAction $action)
    {
        $result = $action->handle($dto);
        return response()->json(['message' => empty($result) ? 'Invalid smart card number' : 'Smart card number verified', 'data' => $result], $result ? 200 : 400);
    }
}
