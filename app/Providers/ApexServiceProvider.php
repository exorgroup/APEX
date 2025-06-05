<?php

namespace App\Providers;

use App\Apex\Core\Widget\WidgetRegistry;
use App\Apex\Core\Widget\WidgetRenderer;
use App\Apex\Widgets\BreadcrumbWidget;
use App\Apex\Widgets\KnobWidget;
use App\Apex\Widgets\DatePickerWidget;
use Illuminate\Support\ServiceProvider;

class ApexServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the widget registry as a singleton
        $this->app->singleton(WidgetRegistry::class, function ($app) {
            $registry = new WidgetRegistry();

            // Register widgets
            $registry->register('breadcrumb', BreadcrumbWidget::class);
            $registry->register('knob', KnobWidget::class);
            $registry->register('datepicker', DatePickerWidget::class);

            return $registry;
        });

        // Register the widget renderer
        $this->app->singleton(WidgetRenderer::class, function ($app) {
            return new WidgetRenderer($app->make(WidgetRegistry::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
