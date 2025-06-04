<?php

namespace App\Apex\Core\Widget;

use App\Apex\Core\Contracts\WidgetInterface;
use InvalidArgumentException;

class WidgetRegistry
{
    protected array $widgets = [];

    /**
     * Register a widget class
     */
    public function register(string $type, string $widgetClass): void
    {
        if (!is_subclass_of($widgetClass, WidgetInterface::class)) {
            throw new InvalidArgumentException("Widget class must implement WidgetInterface");
        }

        $this->widgets[$type] = $widgetClass;
    }

    /**
     * Create a widget instance
     */
    public function create(string $type, array $config = []): WidgetInterface
    {
        if (!isset($this->widgets[$type])) {
            throw new InvalidArgumentException("Widget type '{$type}' is not registered");
        }

        $widgetClass = $this->widgets[$type];
        return new $widgetClass($config);
    }

    /**
     * Check if a widget type is registered
     */
    public function has(string $type): bool
    {
        return isset($this->widgets[$type]);
    }

    /**
     * Get all registered widget types
     */
    public function getTypes(): array
    {
        return array_keys($this->widgets);
    }
}
