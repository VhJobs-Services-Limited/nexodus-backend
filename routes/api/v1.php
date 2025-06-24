<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ChangePasswordController;
use App\Http\Controllers\Api\V1\DeleteAccountController;
use App\Http\Controllers\Api\V1\ForgetPassword\ForgetPasswordController;
use App\Http\Controllers\Api\V1\ForgetPassword\ResetPasswordController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\Registration\EmailVerificationController;
use App\Http\Controllers\Api\V1\Registration\UsernameSuggestionController;
use App\Http\Controllers\Api\V1\Registration\VerifyEmailController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VerifyOtpController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json('Hello world'));

Route::post('email-verification', EmailVerificationController::class)->middleware('throttle:otp')->name('email.verification');

Route::patch('verify-email', VerifyEmailController::class)->name('verify.email');

Route::post('forget-password', ForgetPasswordController::class)->name('forget.password');

Route::patch('reset-password', ResetPasswordController::class)->name('reset.password');

Route::patch('verify-otp', VerifyOtpController::class)->name('verify.otp');


Route::get('username-suggestions/{username}', UsernameSuggestionController::class)->name('username.suggestions');

Route::post('register', [UserController::class, 'store']);

Route::post('login', LoginController::class)->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth', [UserController::class, 'index'])->name('auth');
    Route::patch('auth', [UserController::class, 'update'])->name('auth.update');
    Route::patch('auth/change-password', ChangePasswordController::class)->name('auth.change-password');
    Route::post('auth/trash', DeleteAccountController::class)->name('auth.trash');
});
