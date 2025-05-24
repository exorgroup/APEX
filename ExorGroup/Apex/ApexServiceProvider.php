<?php

namespace ExorGroup\Apex;

use ExorGroup\Apex\TemplateProcessor;
use ExorGroup\Apex\WidgetRegistry;
use ExorGroup\Apex\Support\WidgetDependencyManager;
use ExorGroup\Apex\Support\WidgetGroupManager;
use ExorGroup\Apex\Support\WidgetConnectionManager;
use ExorGroup\Apex\Middleware\ApexTemplateMiddleware;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class ApexServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register core APEX services as singletons
        $this->app->singleton(WidgetRegistry::class);
        $this->app->singleton(WidgetDependencyManager::class);
        $this->app->singleton(WidgetGroupManager::class);
        $this->app->singleton(WidgetConnectionManager::class);

        $this->app->singleton(TemplateProcessor::class, function ($app) {
            return new TemplateProcessor(
                $app->make(WidgetRegistry::class),
                $app->make(WidgetDependencyManager::class),
                $app->make(WidgetGroupManager::class),
                $app->make(WidgetConnectionManager::class)
            );
        });

        // Register APEX facade
        $this->app->singleton('apex', function ($app) {
            return $app->make(TemplateProcessor::class);
        });

        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/apex.php',
            'apex'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register core widgets
        $this->registerCoreWidgets();


        // Register Blade directive
        Blade::directive('apexWidget', function ($expression) {
            // Use a more robust regex to extract the two parts
            preg_match('/^\s*[\'"]([a-zA-Z0-9-]+)[\'"]\s*,\s*([\'"].*[\'"]|\{.*\})\s*$/', $expression, $matches);

            $widgetType = $matches[1] ?? 'unknown';
            $paramsContent = $matches[2] ?? '{}';

            // Try to handle both quoted JSON strings and direct object literals
            if (preg_match('/^\s*[\'"]\{/', $paramsContent) && preg_match('/\}[\'"]\s*$/', $paramsContent)) {
                // Strip quotes from quoted JSON strings
                $paramsContent = preg_replace('/^\s*[\'"](.*)[\'"]\s*$/', '$1', $paramsContent);
            }

            return "!!apex-{$widgetType}:{$paramsContent}!!";
        });

        // Register middleware for template processing
        //$this->app['router']->pushMiddlewareToGroup('web', ApexTemplateMiddleware::class);
        $this->app['router']->pushMiddlewareToGroup('web', \ExorGroup\Apex\Middleware\ApexTemplateMiddleware::class);

        // Publish configuration
        // $this->publishes([
        //     __DIR__ . '/../config/apex.php' => config_path('apex.php'),
        // ], 'apex-config');

        // Publish views
        // $this->publishes([
        //    __DIR__ . '/../resources/views' => resource_path('views/vendor/apex'),
        // ], 'apex-views');

        // Publish assets
        // $this->publishes([
        //     __DIR__ . '/../resources/js' => resource_path('js/vendor/apex'),
        // ], 'apex-assets');

        //////

        // Load config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/apex.php',
            'apex'
        );

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/apex.php' => config_path('apex.php'),
        ], 'apex-config');

        // Load views
        // $this->loadViewsFrom(__DIR__ . '/../resources/views', 'apex');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/apex'),
        ], 'apex-views');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../resources/js' => resource_path('js/vendor/apex'),
        ], 'apex-assets');


        // Load views from the package first, then fall back to the Laravel standard location
        $this->loadViewsFrom([
            resource_path('views/vendor/apex'), // Laravel published views (highest priority)
            resource_path('views/apex'),        // Laravel standard location
            __DIR__ . '/../resources/views',    // Package views (fallback)
        ], 'apex');

        // Also register a way for users to publish the views if they want to customize them
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/apex'),
        ], 'apex-views');
    }

    /**
     * Register core APEX widgets
     */
    protected function registerCoreWidgets(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);

        // Register core widgets
        $registry->register('logo', \ExorGroup\Apex\Widgets\LogoWidget::class);
        $registry->register('testWidget', \ExorGroup\Apex\Widgets\TestWidget::class);

        // You can add more widgets here
    }
}
