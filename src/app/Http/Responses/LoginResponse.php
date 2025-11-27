<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if (is_null($user->profile_image_url) || is_null($user->introduction)) {
            return redirect()->route('profile.edit');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
