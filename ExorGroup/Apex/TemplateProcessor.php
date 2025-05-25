<?php

namespace ExorGroup\Apex;

use ExorGroup\Apex\Support\WidgetDependencyManager;
use ExorGroup\Apex\Support\WidgetGroupManager;
use ExorGroup\Apex\Support\WidgetConnectionManager;
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
        // Enhanced pattern to match both single-line and multiline JSON
        // Using PCRE_DOTALL flag (/s) to make . match newlines
        preg_match_all('/!!apex-([a-zA-Z0-9-]+)(?::(\{(?:[^{}]|(?2))*\}))?!!/s', $content, $matches, PREG_SET_ORDER);

        Log::info('APEX tags found in template', [
            'total_matches' => count($matches),
            'widget_types' => array_map(function ($match) {
                return $match[1];
            }, $matches)
        ]);

        foreach ($matches as $match) {
            $widgetType = $match[1];
            $rawJson = $match[2] ?? '{}';

            // Clean up the JSON string - remove extra whitespace and newlines
            $cleanJson = $this->cleanJsonString($rawJson);

            Log::info('Processing APEX tag', [
                'widget_type' => $widgetType,
                'raw_json_length' => strlen($rawJson),
                'clean_json_length' => strlen($cleanJson),
                'json_preview' => substr($cleanJson, 0, 100) . (strlen($cleanJson) > 100 ? '...' : '')
            ]);

            $params = json_decode($cleanJson, true);

            // Skip if JSON decode failed
            if ($params === null && $cleanJson !== 'null') {
                Log::warning("APEX: Invalid JSON in widget tag", [
                    'widget' => $widgetType,
                    'raw_params' => $rawJson,
                    'clean_params' => $cleanJson,
                    'json_error' => json_last_error_msg()
                ]);
                continue;
            }

            // Ensure we have an array
            if (!is_array($params)) {
                $params = [];
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
     * Clean JSON string to handle multiline formatting
     */
    protected function cleanJsonString(string $json): string
    {
        // Remove extra whitespace while preserving string content
        $json = trim($json);

        // If it's a simple {} return it as is
        if ($json === '{}') {
            return $json;
        }

        // For more complex JSON, we need to be careful about whitespace in strings
        // This regex-based approach handles most common formatting issues
        $json = preg_replace('/\s*:\s*/', ':', $json);  // Remove spaces around colons
        $json = preg_replace('/\s*,\s*/', ',', $json);  // Remove spaces around commas
        $json = preg_replace('/\{\s*/', '{', $json);    // Remove spaces after opening braces
        $json = preg_replace('/\s*\}/', '}', $json);    // Remove spaces before closing braces
        $json = preg_replace('/\[\s*/', '[', $json);    // Remove spaces after opening brackets
        $json = preg_replace('/\s*\]/', ']', $json);    // Remove spaces before closing brackets

        // Handle newlines that might be inside the JSON but outside of strings
        $json = preg_replace('/\s*\n\s*/', '', $json);

        return $json;
    }

    /**
     * Replace widget tags with rendered content
     */
    protected function replaceWidgetTags(string $content): string
    {
        // Enhanced pattern to match both single-line and multiline JSON
        return preg_replace_callback('/!!apex-([a-zA-Z0-9-]+)(?::(\{(?:[^{}]|(?2))*\}))?!!/s', function ($matches) {
            $widgetType = $matches[1];
            $rawJson = $matches[2] ?? '{}';
            $cleanJson = $this->cleanJsonString($rawJson);
            $params = json_decode($cleanJson, true);

            // Skip if JSON decode failed
            if ($params === null && $cleanJson !== 'null') {
                Log::warning("APEX: Invalid JSON in widget replacement", [
                    'widget' => $widgetType,
                    'json_error' => json_last_error_msg()
                ]);
                return "<!-- APEX: Invalid JSON in {$widgetType} widget -->";
            }

            // Ensure we have an array
            if (!is_array($params)) {
                $params = [];
            }

            return $this->renderWidget($widgetType, $params);
        }, $content);
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

        Log::debug("APEX: Registered widget dependency", [
            'id' => $widgetId,
            'type' => $widgetType,
            'targets' => $targets
        ]);
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
     * Render a specific widget
     */
    protected function renderWidget(string $widgetType, array $params): string
    {
        $widgetClass = $this->widgetRegistry->resolve($widgetType);

        if (!$widgetClass) {
            Log::warning("APEX: Widget type '{$widgetType}' not found", [
                'available_widgets' => array_keys($this->widgetRegistry->getRegistered())
            ]);
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
                'trace' => $e->getTraceAsString(),
                'params' => $params
            ]);

            return "<!-- APEX: Error rendering {$widgetType} widget: {$e->getMessage()} -->";
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
