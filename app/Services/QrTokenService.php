<?php

namespace App\Services;

use App\Models\FeedbackQrCode;

class QrTokenService
{
    public const TOKEN_LENGTH = 12;

    public const PREFIX_LENGTH = 6;

    /**
     * Generate a new token.
     *
     * Returns the PLAIN token. The model's `encrypted` cast handles
     * at-rest encryption when the value is assigned to `token_encrypted`.
     *
     * @return array{plain: string, hash: string, prefix: string}
     */
    public function generate(): array
    {
        $plain = $this->randomBase62(self::TOKEN_LENGTH);

        return [
            'plain' => $plain,
            'hash' => hash('sha256', $plain),
            'prefix' => substr($plain, 0, self::PREFIX_LENGTH),
        ];
    }

    public function hash(string $plain): string
    {
        return hash('sha256', $plain);
    }

    public function resolve(string $plain): ?FeedbackQrCode
    {
        if ($plain === '' || strlen($plain) < self::PREFIX_LENGTH) {
            return null;
        }

        $prefix = substr($plain, 0, self::PREFIX_LENGTH);
        $hash = $this->hash($plain);

        return FeedbackQrCode::query()
            ->where('token_prefix', $prefix)
            ->where('token_hash', $hash)
            ->first();
    }

    public function publicUrl(string $plainToken): string
    {
        return url('/feedback/f/'.$plainToken);
    }

    private function randomBase62(int $length): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $chars[random_int(0, 61)];
        }

        return $out;
    }
}
