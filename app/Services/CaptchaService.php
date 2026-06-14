<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class CaptchaService
{
    public const SESSION_KEY = 'captcha_data';
    public const EXPIRE_MINUTES = 5;

    public static function generate(): string
    {
        $first = rand(2, 9);
        $second = rand(1, 9);
        $question = "{$first} + {$second} = ?";

        Session::put(self::SESSION_KEY, [
            'question' => $question,
            'answer' => (string) ($first + $second),
            'expires_at' => Carbon::now()->addMinutes(self::EXPIRE_MINUTES)->timestamp,
        ]);

        return $question;
    }

    public static function getQuestion(): string
    {
        if (! self::hasValidCaptcha()) {
            return self::generate();
        }

        return Session::get(self::SESSION_KEY . '.question', self::generate());
    }

    public static function validate(string $input): bool
    {
        if (! self::hasValidCaptcha()) {
            return false;
        }

        $storedAnswer = Session::get(self::SESSION_KEY . '.answer');

        return trim(strtolower($input)) === trim(strtolower((string) $storedAnswer));
    }

    public static function hasValidCaptcha(): bool
    {
        $data = Session::get(self::SESSION_KEY);

        if (! is_array($data) || ! isset($data['question'], $data['answer'], $data['expires_at'])) {
            return false;
        }

        return Carbon::now()->timestamp <= $data['expires_at'];
    }

    public static function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }
}
