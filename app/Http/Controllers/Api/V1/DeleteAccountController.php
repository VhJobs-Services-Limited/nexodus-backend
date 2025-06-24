<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\User\DeleteAccountAction;
use App\Dtos\User\DeleteAccountDto;
use Illuminate\Http\JsonResponse;

final class DeleteAccountController
{
    public function __invoke(DeleteAccountAction $deleteAccount, DeleteAccountDto $dto): JsonResponse
    {
        $deleteAccount->handle(request()->user(), $dto);

        return response()->json([
            'message' => 'Account deleted successfully',
        ]);
    }
}
