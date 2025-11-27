<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FirstLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        $routeName = $request->route()->getName();
        $method = $request->method();

        \Log::debug('FirstLoginMiddleware triggered', [
            'route' => $routeName,
            'method' => $method,
            'profile_completed' => $user->profile_completed,
        ]);

        if (boolval($user->profile_completed) && $routeName === 'mypage.create' && $method === 'GET') {
            return redirect()->route('items.index');
        }

        if (!boolval($user->profile_completed) && !in_array($routeName, ['mypage.create', 'mypage.store'])) {
            return redirect()->route('mypage.create');
        }

        return $next($request);
    }
}
