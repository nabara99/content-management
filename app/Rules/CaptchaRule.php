<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Session;

class CaptchaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $expected = Session::get('captcha_result');

        if (!$expected || (int) $value !== (int) $expected) {
            $fail('Jawaban captcha salah.');
        }

        Session::forget('captcha_result');
    }
}
