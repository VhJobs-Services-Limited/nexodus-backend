<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\User\ChangePasswordAction;
use App\Dtos\User\ChangePasswordDto;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

final class ChangePasswordController extends Controller
{
    public function __invoke(ChangePasswordDto $dto, ChangePasswordAction $changePassword)
    {
        $changePassword->handle(Auth::user(), $dto);

        return response()->json(['message' => 'Password has been updated']);
    }
}
