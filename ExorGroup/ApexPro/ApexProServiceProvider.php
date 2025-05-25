<?php

namespace ExorGroup\ApexPro;

use Illuminate\Support\ServiceProvider;
use ExorGroup\Apex\WidgetRegistry;

class ApexProServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register pro license checker
        $this->app->singleton('apex.pro.license', function () {
            return new LicenseChecker();
        });
    }

    public function boot(): void
    {
        if (!$this->isLicenseValid()) {
            return;
        }

        $this->registerProWidgets();
        $this->loadProViews();
    }

    protected function isLicenseValid(): bool
    {
        return app('apex.pro.license')->validate();
    }

    protected function registerProWidgets(): void
    {
        $registry = app(WidgetRegistry::class);

        //   $registry->register('dataTable', \ExorGroup\ApexPro\Widgets\DataTableWidget::class);
        //   $registry->register('form', \ExorGroup\ApexPro\Widgets\FormWidget::class);
        //   $registry->register('chart', \ExorGroup\ApexPro\Widgets\ChartWidget::class);
        $registry->register('infoTub', \ExorGroup\ApexPro\Widgets\InfoTubWidget::class);
        $registry->register('imageTub', \ExorGroup\ApexPro\Widgets\ImageTubWidget::class);
    }

    protected function loadProViews1(): void
    {
        // Load views from the package first, then fall back to the Laravel standard location
        $this->loadViewsFrom([
            resource_path('views/vendor/apexpro'), // Laravel published views (highest priority)
            resource_path('views/apexpro'),        // Laravel standard location
            __DIR__ . '/resources/views',          // Package views (fallback)
        ], 'apexpro');

        // Also register a way for users to publish the views if they want to customize them
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/apexpro'),
        ], 'apexpro-views');
    }

    protected function loadProViews(): void
    {
        $viewPaths = [
            resource_path('views/vendor/apexpro'), // Laravel published views (highest priority)
            resource_path('views/apexpro'),        // Laravel standard location
            __DIR__ . '/resources/views',          // Package views (fallback)
        ];

        // Filter to only existing paths
        $existingPaths = array_filter($viewPaths, function ($path) {
            return is_dir($path);
        });

        if (!empty($existingPaths)) {
            $this->loadViewsFrom($existingPaths, 'apexpro');
        } else {
            // If no paths exist, create the package view path
            $packageViewPath = __DIR__ . '/resources/views';
            if (!is_dir($packageViewPath)) {
                @mkdir($packageViewPath, 0755, true);
                @mkdir($packageViewPath . '/widgets', 0755, true);
            }
            $this->loadViewsFrom($packageViewPath, 'apexpro');
        }

        // Publish views
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/apexpro'),
        ], 'apexpro-views');
    }
}
