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

        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(fn () => view('auth.register'));
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::verifyEmailView(fn () => view('auth.verify-email'));

    }
}
