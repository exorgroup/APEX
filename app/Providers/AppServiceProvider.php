<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Load APEX Audit helpers
        if (file_exists(app_path('Apex/Audit/helpers.php'))) {
            require_once app_path('Apex/Audit/helpers.php');
        }
    }
}
