<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    public function generate()
    {
        $operators = ['+', '-', '*', '/'];
        $operator = $operators[array_rand($operators)];

        // Generate numbers based on operator to keep results clean
        switch ($operator) {
            case '/':
                $num2 = rand(1, 9);
                $result = rand(1, 9);
                $num1 = $num2 * $result; // Ensures clean division
                break;
            case '*':
                $num1 = rand(1, 9);
                $num2 = rand(1, 9);
                $result = $num1 * $num2;
                break;
            case '-':
                $num1 = rand(1, 9);
                $num2 = rand(1, $num1); // Ensures positive result
                $result = $num1 - $num2;
                break;
            default: // +
                $num1 = rand(1, 9);
                $num2 = rand(1, 9);
                $result = $num1 + $num2;
                break;
        }

        Session::put('captcha_result', $result);

        $symbol = match ($operator) {
            '*' => '×',
            '/' => '÷',
            default => $operator,
        };

        return response()->json([
            'question' => "{$num1} {$symbol} {$num2} = ?",
        ]);
    }
}
