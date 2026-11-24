<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class AnonymousTokenService
{
    // Token TTL: 90 days
    const TOKEN_TTL_DAYS = 90;

    /**
     * Generate a brand new anonymous tracking token.
     * No user, no email, no IP — just a UUID.
     */
    public function generate(): array
    {
        $token = (string) Str::uuid();
        $secret = Str::random(32); // for report status lookups

        // Store in cache: token => secret mapping
        // Use DB if you want persistence beyond cache
        Cache::put(
            $this->cacheKey($token),
            ['secret' => $secret, 'created_at' => now()->toDateTimeString()],
            now()->addDays(self::TOKEN_TTL_DAYS)
        );

        return [
            'tracking_token' => $token,
            'secret'         => $secret,
            'expires_at'     => now()->addDays(self::TOKEN_TTL_DAYS)->toDateTimeString(),
            'message'        => 'Save this token. It is the only way to track your report. We do not store your identity.',
        ];
    }

    /**
     * Validate a token + secret pair.
     * Used when a witness checks their report status.
     */
    public function validate(string $token, string $secret): bool
    {
        $data = Cache::get($this->cacheKey($token));

        if (!$data) return false;

        return hash_equals($data['secret'], $secret);
    }

    /**
     * Verify just the token exists (without secret — for admin use).
     */
    public function exists(string $token): bool
    {
        return Cache::has($this->cacheKey($token));
    }

    private function cacheKey(string $token): string
    {
        return "anon_token:{$token}";
    }
}