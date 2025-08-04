<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: Service provider for registering Hermes messaging service components
 * 
 * File location: apex/hermes/src/HermesServiceProvider.php
 */

namespace Apex\Hermes;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Apex\Hermes\Console\Commands\GenerateApiKeyCommand;

class HermesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/providers.php',
            'hermes.providers'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/services.php',
            'hermes.services'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish configuration
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/providers.php' => config_path('hermes/providers.php'),
                __DIR__ . '/../config/services.php' => config_path('hermes/services.php'),
            ], 'hermes-config');

            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], 'hermes-migrations');

            // Register commands
            $this->commands([
                GenerateApiKeyCommand::class,
            ]);
        }
    }
}
