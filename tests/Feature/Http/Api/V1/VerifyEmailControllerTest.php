<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\V1;

use App\Models\EmailVerification;

describe('Verify Email', function () {
    beforeEach(function () {
        $this->verification = EmailVerification::factory()->create();
    });

    it('verifies email with valid token', function () {
        $response = $this->patchJson(route('verify.email'), [
            'email' => $this->verification->email,
            'token' => $this->verification->token,
        ]);

        expect($response)
            ->assertOk()
            ->assertJson(
                fn ($json) => $json
                    ->where('message', 'Email verified successfully')
                    ->etc()
            );

        expect(EmailVerification::where('id', $this->verification->id)
            ->whereNotNull('verified_at')
            ->exists())->toBeTrue();
    });

    it('fails with invalid token', function () {
        $response = $this->patchJson(route('verify.email'), [
            'email' => $this->verification->email,
            'token' => '000000',
        ]);

        expect($response)
            ->assertBadRequest()
            ->assertJson(
                fn ($json) => $json
                    ->where('message', 'Invalid verification code')
                    ->etc()
            );
    });

    it('fails with expired token', function () {
        $this->verification = EmailVerification::factory()->expired()->create();

        $response = $this->patchJson(route('verify.email'), [
            'email' => $this->verification->email,
            'token' => $this->verification->token,
        ]);

        expect($response)
            ->assertBadRequest()
            ->assertJson(
                fn ($json) => $json
                    ->where('message', 'Verification code has expired')
                    ->etc()
            );
    });

    it('fails with already verified token', function () {
        $this->verification = EmailVerification::factory()->verified()->create();

        $response = $this->patchJson(route('verify.email'), [
            'email' => $this->verification->email,
            'token' => $this->verification->token,
        ]);

        expect($response)
            ->assertBadRequest()
            ->assertJson(
                fn ($json) => $json
                    ->where('message', 'Email has already been verified')
                    ->etc()
            );
    });
});
