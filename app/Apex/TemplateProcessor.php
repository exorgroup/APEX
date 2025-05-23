<?php

namespace App\Apex;

use App\Apex\Support\WidgetDependencyManager;
use App\Apex\Support\WidgetGroupManager;
use App\Apex\Support\WidgetConnectionManager;
use Illuminate\Support\Facades\Log;

class TemplateProcessor
{
    protected WidgetRegistry $widgetRegistry;
    protected WidgetDependencyManager $dependencyManager;
    protected WidgetGroupManager $groupManager;
    protected WidgetConnectionManager $connectionManager;

    /**
     * Processed widgets for the current request (to prevent data bleeding)
     */
    protected array $processedWidgets = [];

    public function __construct(
        WidgetRegistry $widgetRegistry,
        WidgetDependencyManager $dependencyManager,
        WidgetGroupManager $groupManager,
        WidgetConnectionManager $connectionManager
    ) {
        $this->widgetRegistry = $widgetRegistry;
        $this->dependencyManager = $dependencyManager;
        $this->groupManager = $groupManager;
        $this->connectionManager = $connectionManager;
    }

    /**
     * Process APEX template tags in content
     */
    public function process(string $content): string
    {
        // Reset processed widgets for this request to prevent data bleeding
        $this->processedWidgets = [];

        // First pass: collect all widgets and their dependencies
        $this->collectWidgetDependencies($content);

        // Validate dependencies
        $this->validateDependencies();

        // Second pass: Replace widget tags with rendered content
        $processed = $this->replaceWidgetTags($content);

        // Third pass: Add connection scripts at the end of body
        $processed = $this->addConnectionScripts($processed);

        return $processed;
    }

    /**
     * Collect widgets and their dependencies from content
     */
    protected function collectWidgetDependencies(string $content): void
    {
        // Pattern to match !!apex-widgetName:{"param":"value"}!! format
        //preg_match_all('/!!apex-([a-zA-Z0-9-]+)(?::(\{.*?\}))?!!/', $content, $matches, PREG_SET_ORDER);

        preg_match_all('/!!apex-([a-zA-Z0-9-]+)(?::(\{.*?\}))?!!/', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $widgetType = $match[1];
            $params = isset($match[2]) ? json_decode($match[2], true) : [];

            // Skip if JSON decode failed
            if (isset($match[2]) && $params === null) {
                Log::warning("APEX: Invalid JSON in widget tag", [
                    'widget' => $widgetType,
                    'raw_params' => $match[2]
                ]);
                continue;
            }

            // Ensure widget has an ID
            if (empty($params['id'])) {
                $params['id'] = 'apex-' . $widgetType . '-' . uniqid();
            }

            // Register widget dependencies
            $this->registerWidgetDependency($params['id'], $widgetType, $params);
        }
    }

    /**
     * Register a widget's dependencies
     */
    protected function registerWidgetDependency(string $widgetId, string $widgetType, array $params): void
    {
        // Register with dependency manager
        $targets = [];

        // Handle single target
        if (!empty($params['target'])) {
            $targets = is_array($params['target']) ? $params['target'] : [$params['target']];
        }

        // Handle target groups
        if (!empty($params['targetGroup'])) {
            // This will be resolved later when all widgets are registered
        }

        // Handle widget groups
        if (!empty($params['group'])) {
            $this->groupManager->addToGroup($widgetId, $params['group']);
        }

        $this->dependencyManager->register($widgetId, $widgetType, $targets);

        // Store processed widget parameters (isolated per request)
        $this->processedWidgets[$widgetId] = [
            'type' => $widgetType,
            'params' => $params
        ];
    }

    /**
     * Validate widget dependencies
     */
    protected function validateDependencies(): void
    {
        $missingDependencies = $this->dependencyManager->validateDependencies();

        if (!empty($missingDependencies)) {
            Log::warning('APEX: Widget dependencies not found', [
                'missingDependencies' => $missingDependencies
            ]);
        }
    }

    /**
     * Replace widget tags with rendered content
     */
    protected function replaceWidgetTags(string $content): string
    {
        return preg_replace_callback('/!!apex-([a-zA-Z0-9-]+)(?::(\{.*?\}))?!!/', function ($matches) {
            $widgetType = $matches[1];
            $params = isset($matches[2]) ? json_decode($matches[2], true) : [];

            // Skip if JSON decode failed
            if (isset($matches[2]) && $params === null) {
                return "<!-- APEX: Invalid JSON in {$widgetType} widget -->";
            }

            return $this->renderWidget($widgetType, $params);
        }, $content);
    }

    /**
     * Render a specific widget
     */
    protected function renderWidget(string $widgetType, array $params): string
    {
        $widgetClass = $this->widgetRegistry->resolve($widgetType);

        if (!$widgetClass) {
            Log::warning("APEX: Widget type '{$widgetType}' not found");
            return "<!-- APEX: Widget {$widgetType} not found -->";
        }

        try {
            // Create fresh widget instance to prevent data bleeding
            $widget = app($widgetClass);

            // Ensure widget has an ID
            if (empty($params['id'])) {
                $params['id'] = 'apex-' . $widgetType . '-' . uniqid();
            }

            return $widget->render($params);
        } catch (\Exception $e) {
            Log::error("APEX: Error rendering widget '{$widgetType}'", [
                'error' => $e->getMessage(),
                'params' => $params
            ]);

            return "<!-- APEX: Error rendering {$widgetType} widget -->";
        }
    }

    /**
     * Add connection scripts to the end of the body
     */
    protected function addConnectionScripts(string $content): string
    {
        $connectionScript = $this->dependencyManager->generateConnectionScript();
        $groupScript = $this->groupManager->generateGroupListenersScript();

        if (!empty($connectionScript) || !empty($groupScript)) {
            $script = "<script>\n";

            if (!empty($connectionScript)) {
                $script .= "// Widget connections\n" . $connectionScript . "\n";
            }

            if (!empty($groupScript)) {
                $script .= "// Widget groups\n" . $groupScript . "\n";
            }

            $script .= "</script>";

            // Insert before closing body tag
            $content = str_replace('</body>', $script . "\n</body>", $content);
        }

        return $content;
    }

    /**
     * Get processed widgets for current request
     */
    public function getProcessedWidgets(): array
    {
        return $this->processedWidgets;
    }

    /**
     * Clear processed widgets (for testing or manual cleanup)
     */
    public function clearProcessedWidgets(): void
    {
        $this->processedWidgets = [];
    }
}
