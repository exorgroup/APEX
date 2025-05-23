<?php

namespace App\Providers;

use App\Apex\TemplateProcessor;
use App\Apex\WidgetRegistry;
use App\Apex\Support\WidgetDependencyManager;
use App\Apex\Support\WidgetGroupManager;
use App\Apex\Support\WidgetConnectionManager;
use App\Http\Middleware\ApexTemplateMiddleware;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

use Illuminate\Support\Facades\Log;

class ApexServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register core APEX services as singletons to prevent data bleeding
        $this->app->singleton(WidgetRegistry::class, function ($app) {
            return new WidgetRegistry();
        });

        $this->app->singleton(WidgetDependencyManager::class, function ($app) {
            return new WidgetDependencyManager();
        });

        $this->app->singleton(WidgetGroupManager::class, function ($app) {
            return new WidgetGroupManager();
        });

        $this->app->singleton(WidgetConnectionManager::class, function ($app) {
            return new WidgetConnectionManager();
        });

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register core widgets
        $this->registerCoreWidgets();

        /*
        Blade::directive('apexWidget', function ($expression) {
            // $expression will be something like "'testWidget', ['id' => '...', 'title' => '...']"
            // We need to parse this into widgetType and params
            // This is a simplified example; a robust solution might parse the expression more carefully.
            // For simple cases, we can assume the format is 'widgetType', json_string_of_params

            // This regex extracts the widgetType and the raw JSON string
            preg_match('/^\s*[\'"]([a-zA-Z0-9-]+)[\'"]\s*(?:,\s*(.*))?$/', $expression, $matches);

            $widgetType = $matches[1] ?? 'unknown';
            $rawParams = $matches[2] ?? '{}';

            // Return the raw !!apex-widget:params!! string
            return "!!apex-{$widgetType}:{$rawParams}!!";
        });
        */

        // In ApexServiceProvider's boot method
        Blade::directive('apexWidget', function ($expression) {
            // $expression will now be something like: 'testWidget', '{"id":"...", "title":"..."}'
            // The second argument is already a string containing JSON.

            // Use a more robust regex to extract the two parts:
            // Group 1: widgetType (e.g., 'testWidget')
            // Group 2: rawParams (e.g., '{"id":"...", ...}')
            preg_match('/^\s*[\'"]([a-zA-Z0-9-]+)[\'"]\s*,\s*([\'"].*[\'"])\s*$/', $expression, $matches);

            $widgetType = $matches[1] ?? 'unknown';
            $rawParamsString = $matches[2] ?? '{}'; // This will be the quoted JSON string, e.g., '"{...}"'

            // We need to remove the outer quotes that Blade added to the string literal.
            // Example: '{"id":"test-widget-1", "title":"Test Widget 1"}'
            // If the user used '{"key":"value"}' it becomes '"{\"key\":\"value\"}"' when passed to directive
            // So we need to strip exactly one layer of quotes from the beginning and end.

            // Simpler way: just assume the user passed a JSON string that might be quoted.
            // And if it's not quoted, that's okay, it's just the raw JSON.
            // The preg_match should capture it without extra quotes for this to work.

            // Let's refine the regex slightly to directly capture the JSON content if it's structured.
            // This regex will capture 'testWidget' and then directly the JSON `{...}`
            // if the input was like 'testWidget', {"key":"value"} (not quoted)
            // Or if it was 'testWidget', '{"key":"value"}' (quoted)

            // This regex tries to capture the JSON string itself, correctly handling quotes.
            // It looks for a quoted string for the params.
            preg_match('/^\s*[\'"]([a-zA-Z0-9-]+)[\'"]\s*,\s*([\'"](\{.*?\})[\'"]|\(\{.*?\})|(\{.*?\))\s*$/', $expression, $matches);

            $widgetType = $matches[1] ?? 'unknown';
            $paramsContent = '';

            if (isset($matches[3]) && !empty($matches[3])) { // Case: '{"key":"value"}' (quoted)
                $paramsContent = $matches[3]; // This will be the actual JSON string
            } elseif (isset($matches[4]) && !empty($matches[4])) { // Case: ({"key":"value"})
                $paramsContent = $matches[4]; // This will be the actual JSON string
            } elseif (isset($matches[5]) && !empty($matches[5])) { // Case: {"key":"value"} (unquoted but json format)
                $paramsContent = $matches[5]; // This will be the actual JSON string
            } else {
                // Fallback for unexpected format, could log or default
                $paramsContent = '{}'; // Default to empty JSON
                Log::warning("APEX Directive: Could not parse parameters expression: {$expression}");
            }

            // The `$paramsContent` is now guaranteed to be a raw JSON string (e.g., '{"id":"..."}'),
            // not a PHP expression like 'json_encode($params)'.
            // So we can directly embed it.
            return "!!apex-{$widgetType}:{$paramsContent}!!";
        });


        // Register middleware for template processing
        $this->app['router']->pushMiddlewareToGroup('web', ApexTemplateMiddleware::class);

        // Publish configuration if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/apex.php' => config_path('apex.php'),
            ], 'apex-config');

            $this->publishes([
                __DIR__ . '/../../resources/views/apex' => resource_path('views/apex'),
            ], 'apex-views');

            $this->publishes([
                __DIR__ . '/../../resources/js/apex' => resource_path('js/apex'),
            ], 'apex-assets');
        }
    }

    /**
     * Register core APEX widgets
     */
    protected function registerCoreWidgets(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);

        // Register core layout widgets
        //$registry->register('verticalMenu', \App\Apex\Widgets\VerticalMenuWidget::class);
        //$registry->register('horizontalMenu', \App\Apex\Widgets\HorizontalMenuWidget::class);
        //$registry->register('content', \App\Apex\Widgets\ContentWidget::class);
        //$registry->register('footer', \App\Apex\Widgets\FooterWidget::class);

        // Register navigation widgets
        $registry->register('logo', \App\Apex\Widgets\LogoWidget::class);
        // $registry->register('breadcrumbs', \App\Apex\Widgets\BreadcrumbsWidget::class);
        //$registry->register('pageTitle', \App\Apex\Widgets\PageTitleWidget::class);

        // Register utility widgets
        // $registry->register('date', \App\Apex\Widgets\DateWidget::class);
        //$registry->register('dateTime', \App\Apex\Widgets\DateTimeWidget::class);
        //$registry->register('notifications', \App\Apex\Widgets\NotificationsWidget::class);
        //$registry->register('userMenu', \App\Apex\Widgets\UserMenuWidget::class);
        //$registry->register('themeSwitcher', \App\Apex\Widgets\ThemeSwitcherWidget::class);
        //$registry->register('chat', \App\Apex\Widgets\ChatWidget::class);

        // Register test widget (for development/testing)
        $registry->register('testWidget', \App\Apex\Widgets\TestWidget::class);
    }
}
