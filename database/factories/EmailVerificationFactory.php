<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EmailVerification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailVerification>
 */
final class EmailVerificationFactory extends Factory
{
    protected $model = EmailVerification::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'token' => $this->faker->numerify('######'),
            'expires_at' => now()->addMinutes(30),
            'verified_at' => null,
        ];
    }

    public function verified(): self
    {
        return $this->state(fn (array $attributes) => [
            'verified_at' => now(),
        ]);
    }

    public function expired(): self
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subMinutes(1),
        ]);
    }
}
