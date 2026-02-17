<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    public function generate()
    {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        $result = $num1 + $num2;

        Session::put('captcha_result', $result);

        return response()->json([
            'question' => "{$num1} + {$num2} = ?",
        ]);
    }
}
