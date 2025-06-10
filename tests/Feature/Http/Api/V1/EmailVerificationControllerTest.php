<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\V1;

use App\Mail\EmailVerificationMail;
use App\Models\EmailVerification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class)->in('Feature');

describe('Email Verification Request', function () {
    beforeEach(function () {
        Mail::fake();
    });

    it('can request email verification', function () {
        $response = $this->postJson(route('email.verification'), [
            'email' => 'test1@example.com',
        ]);

        expect($response)
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'expires_at',
                    'expires_at_in_minutes',
                ],
            ]);

        Mail::assertSent(EmailVerificationMail::class);
    });

    it('updates existing email verification', function () {
        EmailVerification::factory()->create(['email' => 'test2@example.com']);

        $response = $this->postJson(route('email.verification'), [
            'email' => 'test2@example.com',
        ]);

        expect($response)
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'expires_at',
                    'expires_at_in_minutes',
                ],
            ]);

        Mail::assertSent(EmailVerificationMail::class);
    });

    it('rate limiting is applied after three attempts', function () {
        $email = 'test@example.com';
        $route = route('api.email.verification');

        Collection::times(3, function () use ($route, $email) {
            $response = $this->postJson($route, ['email' => $email]);
            expect($response)->assertStatus(201);
        });

        // Fourth attempt should be rate limited
        $response = $this->postJson($route, ['email' => $email]);

        expect($response)
            ->assertStatus(429)
            ->assertJson([
                'message' => 'Too Many Attempts.',
            ]);
    });
});
