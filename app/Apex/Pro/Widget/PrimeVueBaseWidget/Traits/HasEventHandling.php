<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Event handling trait for PRO widgets providing event transformation and parameter injection
 * File location: app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits/HasEventHandling.php
 */

namespace App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits;

use Illuminate\Support\Facades\Log;

trait HasEventHandling
{
    /**
     * Transform events configuration
     * @param array $events Events configuration
     * @return array Transformed events configuration
     */
    protected function transformEvents(array $events): array
    {
        try {
            $transformedEvents = [];

            foreach ($events as $eventName => $eventConfig) {
                // Normalize event name (add 'on' prefix if missing)
                $normalizedEventName = $this->normalizeEventName($eventName);

                // Transform event configuration based on type
                if (is_string($eventConfig)) {
                    // Simple string - assume it's JavaScript
                    $transformedEvents[$normalizedEventName] = "js:" . $eventConfig;
                } elseif (is_array($eventConfig)) {
                    // Complex configuration object
                    $type = $eventConfig['type'] ?? 'js';

                    switch ($type) {
                        case 'server':
                            $server = $eventConfig['server'] ?? '';
                            $handler = $eventConfig['handler'] ?? '';
                            $params = $eventConfig['params'] ?? [];
                            $paramsStr = $this->resolveParameterTemplates($params);

                            // Handle response configuration
                            $responseConfig = '';
                            if (isset($eventConfig['response'])) {
                                $transformedResponse = $this->transformResponseConfig($eventConfig['response']);
                                $responseConfig = '|' . base64_encode(json_encode($transformedResponse));
                            }

                            $transformedEvents[$normalizedEventName] = "server:{$server}/{$handler}({$paramsStr}){$responseConfig}";
                            break;

                        case 'vue':
                            $handler = $eventConfig['handler'] ?? '';
                            $params = $eventConfig['params'] ?? [];
                            $paramsStr = $this->resolveParameterTemplates($params);
                            $transformedEvents[$normalizedEventName] = "vue:{$handler}({$paramsStr})";
                            break;

                        case 'js':
                        default:
                            $handler = $eventConfig['handler'] ?? '';
                            // Process parameter injection in JS handler string
                            $processedHandler = $this->processJSParameterInjection($handler);
                            $transformedEvents[$normalizedEventName] = "js:" . $processedHandler;
                            break;
                    }
                } else {
                    // Fallback - pass through as JavaScript
                    $transformedEvents[$normalizedEventName] = "js:" . $eventConfig;
                }
            }

            return $transformedEvents;
        } catch (\Exception $e) {
            Log::error('Error transforming events', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'transformEvents',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $events;
        }
    }

    /**
     * Process parameter injection in JavaScript handler strings
     * @param string $handler JavaScript handler string with parameter templates
     * @return string Processed handler string with resolved parameters
     */
    protected function processJSParameterInjection(string $handler): string
    {
        try {
            // Find all parameter templates in the handler string
            $processedHandler = preg_replace_callback(
                '/\{\{(.+?)\}\}/',
                function ($matches) {
                    $template = $matches[1];
                    return $this->convertTemplateToJS($template);
                },
                $handler
            );

            return $processedHandler;
        } catch (\Exception $e) {
            Log::error('Error processing JS parameter injection', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'processJSParameterInjection',
                'handler' => $handler,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $handler;
        }
    }

    /**
     * Resolve parameter templates to JavaScript expressions
     * @param array $params Array of parameter templates
     * @return string Comma-separated resolved parameters
     */
    protected function resolveParameterTemplates(array $params): string
    {
        try {
            $resolvedParams = [];

            foreach ($params as $param) {
                if (is_string($param) && preg_match('/\{\{(.+?)\}\}/', $param, $matches)) {
                    $template = $matches[1];
                    $resolvedParams[] = $this->convertTemplateToJS($template);
                } else {
                    // Literal value - wrap in quotes if it's a string
                    $resolvedParams[] = is_string($param) ? "'{$param}'" : $param;
                }
            }

            return implode(', ', $resolvedParams);
        } catch (\Exception $e) {
            Log::error('Error resolving parameter templates', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'resolveParameterTemplates',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return implode(', ', $params);
        }
    }

    /**
     * Convert parameter template to JavaScript expression
     * @param string $template Template like "widget:feedback-input-pro.value" or "widget:this.value"
     * @return string JavaScript expression
     */
    protected function convertTemplateToJS(string $template): string
    {
        try {
            $parts = explode(':', $template);
            if (count($parts) !== 2) {
                return "'{$template}'"; // Return as literal if invalid format
            }

            $context = $parts[0];
            $path = $parts[1];

            switch ($context) {
                case 'widget':
                    if (str_starts_with($path, 'this.')) {
                        // Reference to current widget: {{widget:this.value}}
                        $property = substr($path, 5); // Remove 'this.'
                        return "document.getElementById('{$this->getId()}-input').{$property}";
                    } else {
                        // Reference to other widget: {{widget:feedback-input-pro.value}}
                        $pathParts = explode('.', $path);
                        $widgetId = $pathParts[0];
                        $property = $pathParts[1] ?? 'value';
                        return "document.getElementById('{$widgetId}-input').{$property}";
                    }

                case 'static':
                    // Static value: {{static:someValue}}
                    return "'{$path}'";

                case 'form':
                    // Form data: {{form:fieldName}} or {{form:*}}
                    if ($path === '*') {
                        return "getFormData()";
                    } else {
                        return "getFormFieldValue('{$path}')";
                    }

                case 'user':
                    // User session data: {{user:property}}
                    return "getUserProperty('{$path}')";

                default:
                    return "'{$template}'";
            }
        } catch (\Exception $e) {
            Log::error('Error converting template to JS', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'convertTemplateToJS',
                'template' => $template,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return "'{$template}'";
        }
    }

    /**
     * Normalize event name by adding 'on' prefix if missing
     * @param string $eventName Original event name
     * @return string Normalized event name with 'on' prefix
     */
    protected function normalizeEventName(string $eventName): string
    {
        try {
            return str_starts_with($eventName, 'on') ? $eventName : 'on' . ucfirst($eventName);
        } catch (\Exception $e) {
            Log::error('Error normalizing event name', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'normalizeEventName',
                'eventName' => $eventName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $eventName;
        }
    }

    /**
     * Get event handling schema for widget validation
     * @return array JSON schema definition for events
     */
    protected function getEventHandlingSchema(): array
    {
        try {
            return [
                'type' => 'object',
                'description' => 'Event configuration for PRO features',
                'properties' => [
                    'onBlur' => ['type' => 'object', 'description' => 'Blur event configuration'],
                    'onFocus' => ['type' => 'object', 'description' => 'Focus event configuration'],
                    'onChange' => ['type' => 'object', 'description' => 'Change event configuration'],
                    'onInput' => ['type' => 'object', 'description' => 'Input event configuration'],
                    'onKeyDown' => ['type' => 'object', 'description' => 'KeyDown event configuration'],
                    'onKeyUp' => ['type' => 'object', 'description' => 'KeyUp event configuration'],
                    'onClick' => ['type' => 'object', 'description' => 'Click event configuration'],
                    'onDoubleClick' => ['type' => 'object', 'description' => 'Double click event configuration'],
                    'onMouseOver' => ['type' => 'object', 'description' => 'Mouse over event configuration'],
                    'onMouseOut' => ['type' => 'object', 'description' => 'Mouse out event configuration']
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting event handling schema', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'getEventHandlingSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
