<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AirtimeController;
use App\Http\Controllers\Api\V1\Betting\BettingController;
use App\Http\Controllers\Api\V1\Betting\VerifyBettingAccountController;
use App\Http\Controllers\Api\V1\Cable\CableController;
use App\Http\Controllers\Api\V1\Cable\VerifyCableSmartCardController;
use App\Http\Controllers\Api\V1\ChangePasswordController;
use App\Http\Controllers\Api\V1\ClubConnectCallbackController;
use App\Http\Controllers\Api\V1\CreateTransactionPinController;
use App\Http\Controllers\Api\V1\DataController;
use App\Http\Controllers\Api\V1\DeleteAccountController;
use App\Http\Controllers\Api\V1\Electricity\ElectricityController;
use App\Http\Controllers\Api\V1\Electricity\VerifyMetreNumberController;
use App\Http\Controllers\Api\V1\ForgetPassword\ForgetPasswordController;
use App\Http\Controllers\Api\V1\ForgetPassword\ResetPasswordController;
use App\Http\Controllers\Api\V1\InitiateCreateTransactionPinController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\Registration\EmailVerificationController;
use App\Http\Controllers\Api\V1\Registration\UsernameSuggestionController;
use App\Http\Controllers\Api\V1\Registration\VerifyEmailController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VerifyOtpController;
use App\Http\Controllers\Api\V1\Wifi\VerifySmileDeviceController;
use App\Http\Controllers\Api\V1\Wifi\WifiController;
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

Route::get('club-connect-callback', ClubConnectCallbackController::class)->name('club.connect.callback');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth', [UserController::class, 'index'])->name('auth');

    Route::patch('auth', [UserController::class, 'update'])->name('auth.update');

    Route::patch('auth/change-password', ChangePasswordController::class)->name('auth.change-password');

    Route::post('auth/trash', DeleteAccountController::class)->name('auth.trash');

    Route::post('transaction-pin', CreateTransactionPinController::class)->name('create.transaction.pin');

    Route::post('initiate-transaction-pin', InitiateCreateTransactionPinController::class)->name('initiate.transaction.pin');

    Route::apiResource('airtimes', AirtimeController::class)->only(['index', 'store']);

    Route::apiResource('data', DataController::class)->only(['index', 'store']);

    Route::apiResource('cables', CableController::class)->only(['index', 'store']);

    Route::apiResource('electricity', ElectricityController::class)->only(['index', 'store']);

    Route::apiResource('bettings', BettingController::class)->only(['index', 'store']);

    Route::apiResource('wifi', WifiController::class)->only(['index']);

    Route::apiResource('transactions', TransactionController::class)->only(['index', 'show']);

    Route::group(['prefix' => 'verifications'], function () {
        Route::post('betting-account', VerifyBettingAccountController::class)->name('betting.account');
        Route::post('metre-number', VerifyMetreNumberController::class)->name('metre.number');
        Route::post('smile-device', VerifySmileDeviceController::class)->name('smile.device');
        Route::post('cable-smart-card', VerifyCableSmartCardController::class)->name('cable.smart.card');
    });
});
