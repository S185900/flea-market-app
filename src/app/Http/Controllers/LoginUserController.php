<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\LoginRequest;

class LoginUserController extends Controller
{
    // ログイン処理
    public function store(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'auth' => 'ログイン情報が登録されていません',
        ]);
    }
}
