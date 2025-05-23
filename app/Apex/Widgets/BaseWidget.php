<?php

namespace App\Apex\Widgets;

use App\Apex\Contracts\WidgetInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

abstract class BaseWidget implements WidgetInterface
{
    /**
     * Widget instance ID (unique per render)
     */
    protected string $id;

    /**
     * Whether this widget uses events
     */
    protected bool $usesEvents = false;

    /**
     * Events this widget publishes
     */
    protected array $publishedEvents = [];

    /**
     * Events this widget subscribes to
     */
    protected array $subscribedEvents = [];

    /**
     * Widget parameters for current render
     */
    protected array $params = [];

    /**
     * Render the widget with the given parameters
     */
    public function render(array $params = []): string
    {
        // Reset instance data to prevent bleeding between renders
        $this->resetInstance();

        // Merge with defaults and validate
        $this->params = $this->mergeWithDefaults($params);

        // Validate parameters
        $errors = $this->validateParams($this->params);
        if (!empty($errors)) {
            Log::warning("APEX: Widget validation errors", [
                'widget' => static::class,
                'errors' => $errors,
                'params' => $this->params
            ]);

            return $this->renderError('Invalid widget parameters', $errors);
        }

        // Set or generate widget ID
        $this->id = $this->params['id'] ?? $this->generateId();
        $this->params['id'] = $this->id;

        // Add event configuration if widget uses events
        if ($this->usesEvents) {
            $this->params['_events'] = $this->getEventConfig();
        }

        try {
            return $this->renderWidget($this->params);
        } catch (\Exception $e) {
            Log::error("APEX: Error rendering widget", [
                'widget' => static::class,
                'error' => $e->getMessage(),
                'params' => $this->params
            ]);

            return $this->renderError('Widget rendering failed', [$e->getMessage()]);
        }
    }

    /**
     * Reset instance data to prevent bleeding between renders
     */
    protected function resetInstance(): void
    {
        $this->params = [];
        $this->id = '';
    }

    /**
     * Generate a unique widget ID
     */
    protected function generateId(): string
    {
        $className = class_basename(static::class);
        $widgetName = strtolower(str_replace('Widget', '', $className));

        return 'apex-' . $widgetName . '-' . uniqid();
    }

    /**
     * Merge user parameters with defaults
     */
    protected function mergeWithDefaults(array $params): array
    {
        return array_merge($this->getDefaults(), $params);
    }

    /**
     * Get default parameters for the widget
     */
    public function getDefaults(): array
    {
        return [
            'id' => null,
            'cssClass' => '',
        ];
    }

    /**
     * Validate widget parameters
     */
    public function validateParams(array $params): array
    {
        $errors = [];

        // Add common validation rules here
        // Individual widgets can override this method for specific validation

        return $errors;
    }

    /**
     * Get the widget's event configuration
     */
    public function getEventConfig(): array
    {
        return [
            'publishes' => $this->publishedEvents,
            'subscribes' => $this->subscribedEvents,
        ];
    }

    /**
     * Check if this widget uses events
     */
    public function usesEvents(): bool
    {
        return $this->usesEvents;
    }

    /**
     * Actual widget rendering - to be implemented by child classes
     */
    abstract protected function renderWidget(array $params): string;

    /**
     * Render widget view with parameters
     */
    protected function view(string $viewName, array $data = []): string
    {
        $viewPath = 'apex.widgets.' . $viewName;

        if (!View::exists($viewPath)) {
            Log::warning("APEX: Widget view '{$viewPath}' not found");
            return $this->renderError('Widget view not found', ["View: {$viewPath}"]);
        }

        try {
            return view($viewPath, array_merge($this->params, $data))->render();
        } catch (\Exception $e) {
            Log::error("APEX: Error rendering widget view", [
                'view' => $viewPath,
                'error' => $e->getMessage()
            ]);

            return $this->renderError('Widget view rendering failed', [$e->getMessage()]);
        }
    }

    /**
     * Resolve route or URL from parameters
     */
    protected function resolveUrl(array $params): string
    {
        if (!empty($params['url'])) {
            return $params['url'];
        }

        if (!empty($params['route'])) {
            try {
                return route($params['route']);
            } catch (\Exception $e) {
                Log::warning("APEX: Invalid route '{$params['route']}'", [
                    'error' => $e->getMessage()
                ]);
                return '#';
            }
        }

        return '#';
    }

    /**
     * Resolve icon based on configuration
     */
    protected function resolveIcon(string $icon): string
    {
        $provider = config('apex.icons.provider', 'prime');
        $prefix = config('apex.icons.prefix', 'pi pi-');

        // Handle custom SVG icons
        if ($provider === 'custom' && strpos($icon, '/') !== false) {
            return '<img src="' . $icon . '" alt="Icon" class="apex-icon" />';
        }

        // Handle raw SVG content
        if ($provider === 'svg' && strpos($icon, '<svg') !== false) {
            return $icon;
        }

        // Default to icon classes
        return '<i class="' . $prefix . $icon . '"></i>';
    }

    /**
     * Render error message
     */
    protected function renderError(string $message, array $details = []): string
    {
        if (config('app.debug', false)) {
            $errorDetails = !empty($details) ? ' (' . implode(', ', $details) . ')' : '';
            return "<!-- APEX ERROR: {$message}{$errorDetails} -->";
        }

        return "<!-- APEX: Widget error -->";
    }

    /**
     * Get current widget ID
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get current widget parameters
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
