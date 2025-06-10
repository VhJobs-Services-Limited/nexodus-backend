<?php

declare(strict_types=1);

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * Todos:
 * Need to burst the cache when user is being updated
 * If we need to set token expiration, we need to set it to match the cache TTL
 */
final class PersonalAccessToken extends SanctumPersonalAccessToken
{
    // public static function boot()
    // {
    //     parent::boot();
    //     // When updating, cancel normal update and manually update
    //     // the table asynchronously every hour.
    //     static::updating(function (self $personalAccessToken) {

    //         try {
    //             cache()->remember("PersonalAccessToken::lastUsgeUpdate", 3600, function () use ($personalAccessToken) {
    //                 dispatch(new UpdatePersonalAccessToken($personalAccessToken, $personalAccessToken->getDirty()));
    //                 return now();
    //             });
    //         } catch (\Exception $e) {
    //             logger()->critical($e->getMessage());
    //         }
    //         return false;

    //     });
    // }
    public static function findToken($token)
    {
        $token = cache()->store('file')->remember("PersonalAccessToken::$token", 600, fn () => parent::findToken($token) ?? '_null_');
        if ($token === '_null_') {
            return null;
        }

        return $token;
    }

    /**
     * Limit saving of PersonalAccessToken records
     *
     * We only want to actually save when there is something other than
     * the last_used_at column that has changed. It prevents extra DB writes
     * since we aren't going to use that column for anything.
     *
     * @return bool
     */
    public function save(array $options = [])
    {
        $changes = $this->getDirty();
        // Check for 2 changed values because one is always the updated_at column
        if (! array_key_exists('last_used_at', $changes) || count($changes) > 2) {
            parent::save();
        }

        return false;
    }

    public function getTokenableAttribute()
    {
        return cache()->store('file')->remember("PersonalAccessToken::{$this->id}::tokenable", 600, fn () => parent::tokenable()->first());
    }
}
