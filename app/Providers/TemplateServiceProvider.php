<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;

class TemplateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share template configuration with all views
        View::share('currentTemplate', config('apex.template'));
        View::share('templateConfig', config('apex.templates.' . config('apex.template')));

        // Share template information with Inertia
        Inertia::share('template', function () {
            return [
                'current' => config('apex.template'),
                'config' => config('apex.templates.' . config('apex.template')),
            ];
        });
    }
}
