<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route as RouteFacade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure the 'role' middleware alias is registered at runtime in case Kernel mapping isn't picked up.
        if (method_exists(app('router'), 'aliasMiddleware')) {
            app('router')->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
        }
    }
}
