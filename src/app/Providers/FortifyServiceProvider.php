<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fortify::createUsersUsing(CreateNewUser::class);

        // Fortifyのデフォルトルートを無効化
        Fortify::ignoreRoutes();

        // ユーザー作成アクションの指定
        Fortify::createUsersUsing(CreateNewUser::class);

        // ビューのオーバーライド
        Fortify::registerView(fn () => view('auth.register'));
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::verifyEmailView(fn () => view('auth.verify-email'));

        // フォームリクエストのバインド
        $this->app->bind(
            \Laravel\Fortify\Http\Requests\LoginRequest::class,
            \App\Http\Requests\LoginRequest::class
        );

    }
}
