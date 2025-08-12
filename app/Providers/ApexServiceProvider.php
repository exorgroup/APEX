<?php

/**
 * File location: app/Providers/ApexServiceProvider.php
 * Description: Updated APEX Service Provider with Core InputText widget registration
 */

namespace App\Providers;

use App\Apex\Core\Widget\WidgetRegistry;
use App\Apex\Core\Widget\WidgetRenderer;
use App\Apex\Core\Template\TemplateManager;
use App\Apex\Widgets\BreadcrumbWidget;
use App\Apex\Widgets\KnobWidget;
use App\Apex\Widgets\DatePickerWidget;
use App\Apex\Core\Widgets\Forms\InputText\InputTextWidget; // Updated to Core version
use App\Apex\Widgets\InputNumberWidget;
use App\Apex\Widgets\TextareaWidget;
use App\Apex\Widgets\SelectWidget;
use App\Apex\Widgets\CheckboxWidget;
use App\Apex\Widgets\ButtonWidget;
use App\Apex\Widgets\DataTableWidget;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class ApexServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        try {
            // Register the template manager
            $this->app->singleton(TemplateManager::class, function ($app) {
                return new TemplateManager();
            });

            // Register the widget registry as a singleton
            $this->app->singleton(WidgetRegistry::class, function ($app) {
                $registry = new WidgetRegistry();

                // Register widgets
                //  $registry->register('breadcrumb', BreadcrumbWidget::class);
                //  $registry->register('knob', KnobWidget::class);
                //  $registry->register('datepicker', DatePickerWidget::class);
                // $registry->register('inputtext', InputTextWidget::class); // Now using Core version
                $registry->register('inputtext', \App\Apex\Core\Widgets\Forms\InputText\InputTextWidget::class); // This works
                //   $registry->register('inputnumber', InputNumberWidget::class);
                //   $registry->register('textarea', TextareaWidget::class);
                //   $registry->register('select', SelectWidget::class);
                //   $registry->register('checkbox', CheckboxWidget::class);
                //   $registry->register('button', ButtonWidget::class);
                //   $registry->register('datatable', DataTableWidget::class);

                return $registry;
            });

            // Register the widget renderer
            $this->app->singleton(WidgetRenderer::class, function ($app) {
                return new WidgetRenderer($app->make(WidgetRegistry::class));
            });
        } catch (\Exception $e) {
            Log::error('Error in ApexServiceProvider register method', [
                'file' => 'app/Providers/ApexServiceProvider.php',
                'method' => 'register',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            // Publish config file
            $this->publishes([
                __DIR__ . '/../../config/apex.php' => config_path('apex.php'),
            ], 'apex-config');
        } catch (\Exception $e) {
            Log::error('Error in ApexServiceProvider boot method', [
                'file' => 'app/Providers/ApexServiceProvider.php',
                'method' => 'boot',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
