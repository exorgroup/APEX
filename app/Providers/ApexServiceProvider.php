<?php

namespace App\Providers;

use App\Apex\Core\Widget\WidgetRegistry;
use App\Apex\Core\Widget\WidgetRenderer;
use App\Apex\Core\Template\TemplateManager;
use App\Apex\Widgets\BreadcrumbWidget;
use App\Apex\Widgets\KnobWidget;
use App\Apex\Widgets\DatePickerWidget;
use App\Apex\Widgets\InputTextWidget;
use App\Apex\Widgets\InputNumberWidget;
use App\Apex\Widgets\TextareaWidget;
use App\Apex\Widgets\SelectWidget;
use App\Apex\Widgets\CheckboxWidget;
use App\Apex\Widgets\ButtonWidget;

use Illuminate\Support\ServiceProvider;

class ApexServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the template manager
        $this->app->singleton(TemplateManager::class, function ($app) {
            return new TemplateManager();
        });

        // Register the widget registry as a singleton
        $this->app->singleton(WidgetRegistry::class, function ($app) {
            $registry = new WidgetRegistry();

            // Register widgets
            $registry->register('breadcrumb', BreadcrumbWidget::class);
            $registry->register('knob', KnobWidget::class);
            $registry->register('datepicker', DatePickerWidget::class);
            $registry->register('inputtext', InputTextWidget::class);
            $registry->register('inputnumber', InputNumberWidget::class);
            $registry->register('textarea', TextareaWidget::class);
            $registry->register('select', SelectWidget::class);
            $registry->register('checkbox', CheckboxWidget::class);
            $registry->register('button', ButtonWidget::class);


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
        // Publish config file
        $this->publishes([
            __DIR__ . '/../../config/apex.php' => config_path('apex.php'),
        ], 'apex-config');
    }
}
