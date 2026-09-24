<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class MathCaptchaService
{
    private const SESSION_KEY = 'admin_captcha';

    private const EXPIRY_SECONDS = 300; // 5 minutes

    /**
     * Generate a new math question and store the answer in session.
     *
     * @return array{question: string, token: string}
     */
    public function generate(): array
    {
        $operations = ['+', '-', '×'];
        $op = $operations[array_rand($operations)];

        [$a, $b, $answer] = $this->buildQuestion($op);

        $token = bin2hex(random_bytes(16));

        Session::put(self::SESSION_KEY, [
            'answer' => $answer,
            'token' => $token,
            'expires_at' => now()->addSeconds(self::EXPIRY_SECONDS)->timestamp,
        ]);

        return [
            'question' => "{$a} {$op} {$b}",
            'token' => $token,
        ];
    }

    /**
     * Verify the submitted answer against the session-stored value.
     */
    public function verify(string $answer, ?string $token = null): bool
    {
        $stored = Session::get(self::SESSION_KEY);

        // Consume the captcha regardless of outcome — one-shot use.
        Session::forget(self::SESSION_KEY);

        if (! is_array($stored)) {
            return false;
        }

        if (($stored['expires_at'] ?? 0) < now()->timestamp) {
            return false;
        }

        if ($token !== null && ! hash_equals((string) $stored['token'], $token)) {
            return false;
        }

        $submitted = (int) preg_replace('/[^0-9\-]/', '', $answer);

        return hash_equals((string) $stored['answer'], (string) $submitted);
    }

    /**
     * @return array{0:int,1:int,2:int} [a, b, answer]
     */
    private function buildQuestion(string $op): array
    {
        switch ($op) {
            case '-':
                $a = random_int(10, 99);
                $b = random_int(1, $a); // ensures non-negative answer

                return [$a, $b, $a - $b];

            case '×':
                $a = random_int(2, 12);
                $b = random_int(2, 12);

                return [$a, $b, $a * $b];

            case '+':
            default:
                $a = random_int(1, 20);
                $b = random_int(1, 20);

                return [$a, $b, $a + $b];
        }
    }
}
