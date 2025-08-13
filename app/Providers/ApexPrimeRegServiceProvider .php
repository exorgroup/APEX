<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Service provider for APEX framework registration and configuration
 * File location: app/Providers/ApexPrimeRegServiceProvider.php
 */

namespace App\Providers;

use App\Apex\PrimeReg\Commands\ScanPrimeVue;
use App\Apex\PrimeReg\Commands\DiagnosePrimeVue;
use App\Apex\PrimeReg\Services\ComponentScanner;
use App\Apex\PrimeReg\Services\CurationValidator;
use App\Apex\PrimeReg\Services\RegistryGenerator;
use App\Apex\Core\Services\LicenseService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class ApexPrimeRegServiceProvider  extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        try {
            // Register PrimeReg services (development tools)
            $this->registerPrimeRegServices();

            // Register Core services
            $this->registerCoreServices();

            // Register commands
            $this->registerCommands();
        } catch (\Exception $e) {
            Log::error('Error in ApexPrimeRegServiceProvider register method', [
                'folder' => 'app/Providers',
                'file' => 'ApexPrimeRegServiceProvider.php',
                'method' => 'register',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        try {
            // Publish configuration files
            $this->publishConfiguration();

            // Load routes if needed
            $this->loadRoutes();

            // Load views if needed
            $this->loadViews();
        } catch (\Exception $e) {
            Log::error('Error in ApexPrimeRegServiceProvider boot method', [
                'folder' => 'app/Providers',
                'file' => 'ApexPrimeRegServiceProvider.php',
                'method' => 'boot',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Register PrimeReg development tool services
     *
     * @return void
     */
    private function registerPrimeRegServices(): void
    {
        try {
            // Register ComponentScanner as singleton
            $this->app->singleton(ComponentScanner::class, function ($app) {
                return new ComponentScanner();
            });

            // Register CurationValidator as singleton
            $this->app->singleton(CurationValidator::class, function ($app) {
                return new CurationValidator();
            });

            // Register RegistryGenerator as singleton
            $this->app->singleton(RegistryGenerator::class, function ($app) {
                return new RegistryGenerator();
            });
        } catch (\Exception $e) {
            Log::error('Error registering PrimeReg services', [
                'folder' => 'app/Providers',
                'file' => 'ApexPrimeRegServiceProvider.php',
                'method' => 'registerPrimeRegServices',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Register Core framework services
     *
     * @return void
     */
    private function registerCoreServices(): void
    {
        try {
            // Register LicenseService as singleton
            $this->app->singleton(LicenseService::class, function ($app) {
                return new LicenseService();
            });

            // Register other core services as they are created
            // $this->app->singleton(PrimeVueApiRegistry::class, function ($app) {
            //     return new PrimeVueApiRegistry();
            // });

        } catch (\Exception $e) {
            Log::error('Error registering Core services', [
                'folder' => 'app/Providers',
                'file' => 'ApexPrimeRegServiceProvider.php',
                'method' => 'registerCoreServices',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Register Artisan commands
     *
     * @return void
     */
    private function registerCommands(): void
    {
        try {
            if ($this->app->runningInConsole()) {
                $this->commands([
                    ScanPrimeVue::class,
                    DiagnosePrimeVue::class,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error registering commands', [
                'folder' => 'app/Providers',
                'file' => 'ApexPrimeRegServiceProvider.php',
                'method' => 'registerCommands',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Publish configuration files
     *
     * @return void
     */
    private function publishConfiguration(): void
    {
        try {
            if ($this->app->runningInConsole()) {
                // Publish APEX configuration
                $this->publishes([
                    __DIR__ . '/../../config/apex.php' => config_path('apex.php'),
                ], 'apex-config');

                // Publish APEX migrations
                $this->publishes([
                    __DIR__ . '/../../database/migrations/create_apex_registry_table.php'
                    => database_path('migrations/' . date('Y_m_d_His') . '_create_apex_registry_table.php'),
                ], 'apex-migrations');

                // Publish APEX assets
                $this->publishes([
                    __DIR__ . '/../../resources/js/components/apex' => resource_path('js/components/apex'),
                ], 'apex-assets');
            }
        } catch (\Exception $e) {
            Log::error('Error publishing configuration', [
                'folder' => 'app/Providers',
                'file' => 'ApexPrimeRegServiceProvider.php',
                'method' => 'publishConfiguration',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw here as this is not critical
        }
    }

    /**
     * Load APEX routes if they exist
     *
     * @return void
     */
    private function loadRoutes(): void
    {
        try {
            $routesPath = __DIR__ . '/../../routes/apex.php';

            if (file_exists($routesPath)) {
                $this->loadRoutesFrom($routesPath);
            }
        } catch (\Exception $e) {
            Log::error('Error loading routes', [
                'folder' => 'app/Providers',
                'file' => 'ApexPrimeRegServiceProvider.php',
                'method' => 'loadRoutes',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw here as routes are optional
        }
    }

    /**
     * Load APEX views if they exist
     *
     * @return void
     */
    private function loadViews(): void
    {
        try {
            $viewsPath = __DIR__ . '/../../resources/views/apex';

            if (is_dir($viewsPath)) {
                $this->loadViewsFrom($viewsPath, 'apex');
            }
        } catch (\Exception $e) {
            Log::error('Error loading views', [
                'folder' => 'app/Providers',
                'file' => 'ApexPrimeRegServiceProvider.php',
                'method' => 'loadViews',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw here as views are optional
        }
    }

    /**
     * Get the services provided by the provider
     *
     * @return array Array of service class names
     */
    public function provides(): array
    {
        try {
            return [
                ComponentScanner::class,
                CurationValidator::class,
                RegistryGenerator::class,
                LicenseService::class,
                ScanPrimeVue::class,
                DiagnosePrimeVue::class,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting provided services', [
                'folder' => 'app/Providers',
                'file' => 'ApexPrimeRegServiceProvider.php',
                'method' => 'provides',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
