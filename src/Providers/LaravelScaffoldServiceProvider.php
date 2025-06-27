<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Providers;

use Illuminate\Support\ServiceProvider;
use JoeSu\LaravelScaffold\Commands\MakeRepositoryCommand;

class LaravelScaffoldServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/laravel-scaffold.php',
            'laravel-scaffold'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load language files
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'laravel-scaffold');

        // Register Artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRepositoryCommand::class,
            ]);

            // Publish configuration
            $this->publishes([
                __DIR__ . '/../../config/laravel-scaffold.php' => config_path('laravel-scaffold.php'),
            ], 'config');

            // Publish language files
            $this->publishes([
                __DIR__ . '/../../resources/lang' => resource_path('lang/vendor/laravel-scaffold'),
            ], 'lang');
        }

        // Publish README
        $this->publishes([
            __DIR__ . '/../../README.md' => base_path('laravel-scaffold-README.md'),
        ], 'docs');
    }
}
