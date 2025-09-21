<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Crypto\SellCryptoAction;
use App\Dtos\Crypto\SellCryptoDto;

final class SellCryptoController
{
    public function __invoke(SellCryptoDto $dto, SellCryptoAction $action)
    {
        $response = $action->handle($dto);

        return response()->json([
            'data' => $response,
        ]);
    }
}
