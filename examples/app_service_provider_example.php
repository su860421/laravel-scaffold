<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\UserServiceInterface;
use App\Services\UserService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // User Service Binding
        $this->app->bind(UserServiceInterface::class, UserService::class);

        // You can add other service bindings here
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
