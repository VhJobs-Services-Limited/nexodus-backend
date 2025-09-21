<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Wifi;

use App\Actions\Bill\VerifySmileDeviceAction;
use App\Dtos\Bill\VerifySmileDeviceDto;

final class VerifySmileDeviceController
{
    public function __invoke(VerifySmileDeviceDto $dto, VerifySmileDeviceAction $action)
    {
        $result = $action->handle($dto);

        return response()->json(['message' => empty($result) ? 'Invalid device id' : 'Device id verified', 'data' => $result], $result ? 200 : 400);
    }
}
