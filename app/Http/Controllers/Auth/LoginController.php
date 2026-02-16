<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\CaptchaRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'captcha' => ['required', new CaptchaRule],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'captcha.required' => 'Captcha harus diisi.',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (!Auth::user()->isActive()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif. Hubungi administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/user/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
