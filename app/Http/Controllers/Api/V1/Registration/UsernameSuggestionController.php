<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Registration;

use App\Models\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UsernameSuggestionController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $username): Response
    {
        if (strlen($username) < 3) {
            throw new BadRequestHttpException('Username must be at least 3 characters long');
        }

        if (! preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new BadRequestHttpException('Username can only contain letters, numbers, and underscores');
        }

        $baseUsername = Str::lower($username);
        $exists = User::where('username', $baseUsername)->exists();
        $suggestions = $this->generateSuggestions($baseUsername);

        return response()->json([
            'message' => $exists ? 'Username is already chosen, try these:' : 'Username is available',
            'data' => [
                'suggestions' => $suggestions,
                'is_available' => ! $exists,
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Generate unique username suggestions.
     *
     * @return array<int, string>
     */
    private function generateSuggestions(string $username): array
    {
        $baseUsername = Str::lower($username);
        $words = explode('_', $baseUsername);
        $firstWord = $words[0] ?? $baseUsername;
        $secondWord = $words[1] ?? '';

        // Generate all possible suggestions first
        $possibleSuggestions = collect([
            $baseUsername,
            // Simple number combinations
            ...collect(range(1, 2))->map(fn ($num) => strtolower("$baseUsername$num")),
            // Creative combinations
            strtolower("{$firstWord}_official"),
            strtolower("{$firstWord}_verified"),
            strtolower("{$firstWord}_pro"),
            strtolower("real_{$firstWord}"),
            strtolower("the_{$firstWord}"),
            // Underscore combinations
            ...collect(range(1, 2))->map(fn ($num) => strtolower("{$firstWord}_{$num}")),
            ...collect(range(1, 2))->map(fn ($num) => strtolower("{$firstWord}_user_{$num}")),
            // Random string combinations
            ...collect(range(1, 2))->map(fn () => strtolower("{$firstWord}_" . Str::random(4))),
            ...collect(range(1, 2))->map(fn () => strtolower(Str::random(4)) . "_{$firstWord}"),
        ]);

        $existingUsernames = User::whereIn('username', $possibleSuggestions)
            ->pluck('username')
            ->toArray();

        return $possibleSuggestions
            ->reject(fn ($suggestion) => in_array($suggestion, $existingUsernames))
            ->random(5)
            ->values()
            ->toArray();
    }
}
