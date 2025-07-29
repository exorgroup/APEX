<?php

/*
Copyright EXOR Group ltd 2025 
Version 1.0.0.0 
APEX Laravel Auditing
Description: Service provider for APEX Laravel Auditing package. Registers audit services, observers, and configuration for comprehensive forensic-grade audit trails with digital signatures and rollback capabilities.
*/

namespace App\Apex\Audit\Providers;

use Illuminate\Support\ServiceProvider;
use App\Apex\Audit\Services\AuditService;
use App\Apex\Audit\Services\AuditSignatureService;
use App\Apex\Audit\Services\HistoryService;
use App\Apex\Audit\Services\RollbackService;
use App\Apex\Audit\Middleware\ApexAuditConfig;
use App\Apex\Audit\Console\Commands\AuditVerifyCommand;
use App\Apex\Audit\Console\Commands\AuditCleanupCommand;

class AuditServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/audit.php',
            'apex.audit'
        );

        $this->app->singleton(AuditSignatureService::class);
        $this->app->singleton(AuditService::class);
        $this->app->singleton(HistoryService::class);
        $this->app->singleton(RollbackService::class);
        $this->app->singleton(\App\Apex\Audit\Services\ApexAuditLanguageService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerMigrations();
        $this->registerMiddleware();
        $this->registerCommands();
        $this->publishAssets();
    }

    /**
     * Register audit migrations.
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register middleware.
     */
    protected function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('apex.audit.config', ApexAuditConfig::class);
    }

    /**
     * Register console commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AuditVerifyCommand::class,
                AuditCleanupCommand::class,
            ]);
        }
    }

    /**
     * Publish package assets.
     */
    protected function publishAssets(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/audit.php' => config_path('apex/audit.php'),
            ], 'apex-audit-config');

            $this->publishes([
                __DIR__ . '/../Database/Migrations' => database_path('migrations'),
            ], 'apex-audit-migrations');

            $this->publishes([
                __DIR__ . '/../Lang' => resource_path('lang/vendor/apex-audit'),
            ], 'apex-audit-lang');
        }

        // Load helper functions
        if (file_exists(__DIR__ . '/../helpers.php')) {
            require_once __DIR__ . '/../helpers.php';
        }
    }
}
