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
    }

    protected function loadProViews(): void
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
}
