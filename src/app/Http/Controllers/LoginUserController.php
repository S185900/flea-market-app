<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginUserController extends Controller
{
    public function store(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
            }

            return redirect()->intended('/'); // ログイン後のリダイレクト先(元のページに戻る)
        }

        return back()->withErrors([
            'password' => 'ログイン情報が登録されていません',
        ]);
    }
}
