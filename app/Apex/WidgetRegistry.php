<?php

namespace App\Apex;

use Illuminate\Support\Facades\Log;

class WidgetRegistry
{
    /**
     * Registered widgets
     */
    protected array $widgets = [];

    /**
     * Widget aliases (for backward compatibility or shortcuts)
     */
    protected array $aliases = [];

    /**
     * Register a widget class
     */
    public function register(string $name, string $widgetClass): self
    {
        // Validate widget class exists
        if (!class_exists($widgetClass)) {
            Log::warning("APEX: Widget class '{$widgetClass}' does not exist");
            return $this;
        }

        // Validate widget implements required interface
        $reflection = new \ReflectionClass($widgetClass);

        if (!$reflection->isSubclassOf(\App\Apex\Contracts\WidgetInterface::class)) {
            Log::warning("APEX: Widget class '{$widgetClass}' must implement WidgetInterface");
            return $this;
        }

        $this->widgets[$name] = $widgetClass;

        Log::debug("APEX: Registered widget '{$name}' with class '{$widgetClass}'");

        return $this;
    }

    /**
     * Register a widget alias
     */
    public function alias(string $alias, string $widgetName): self
    {
        if (!$this->exists($widgetName)) {
            Log::warning("APEX: Cannot create alias '{$alias}' for non-existent widget '{$widgetName}'");
            return $this;
        }

        $this->aliases[$alias] = $widgetName;

        Log::debug("APEX: Created alias '{$alias}' for widget '{$widgetName}'");

        return $this;
    }

    /**
     * Resolve a widget class by name or alias
     */
    public function resolve(string $name): ?string
    {
        // Check if it's an alias first
        if (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }

        return $this->widgets[$name] ?? null;
    }

    /**
     * Check if a widget exists
     */
    public function exists(string $name): bool
    {
        // Check direct registration
        if (isset($this->widgets[$name])) {
            return true;
        }

        // Check aliases
        if (isset($this->aliases[$name])) {
            return isset($this->widgets[$this->aliases[$name]]);
        }

        return false;
    }

    /**
     * Get all registered widgets
     */
    public function getRegistered(): array
    {
        return $this->widgets;
    }

    /**
     * Get all registered aliases
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * Unregister a widget
     */
    public function unregister(string $name): self
    {
        unset($this->widgets[$name]);

        // Remove any aliases pointing to this widget
        $this->aliases = array_filter($this->aliases, function ($target) use ($name) {
            return $target !== $name;
        });

        Log::debug("APEX: Unregistered widget '{$name}'");

        return $this;
    }

    /**
     * Register multiple widgets at once
     */
    public function registerMany(array $widgets): self
    {
        foreach ($widgets as $name => $class) {
            $this->register($name, $class);
        }

        return $this;
    }

    /**
     * Get widget metadata (for debugging/inspection)
     */
    public function getWidgetMetadata(string $name): ?array
    {
        $class = $this->resolve($name);

        if (!$class) {
            return null;
        }

        try {
            $reflection = new \ReflectionClass($class);

            return [
                'name' => $name,
                'class' => $class,
                'file' => $reflection->getFileName(),
                'namespace' => $reflection->getNamespaceName(),
                'methods' => array_map(function ($method) {
                    return $method->getName();
                }, $reflection->getMethods(\ReflectionMethod::IS_PUBLIC)),
                'properties' => array_map(function ($property) {
                    return $property->getName();
                }, $reflection->getProperties(\ReflectionProperty::IS_PUBLIC)),
            ];
        } catch (\Exception $e) {
            Log::error("APEX: Error getting metadata for widget '{$name}'", [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Clear all registered widgets (for testing)
     */
    public function clear(): self
    {
        $this->widgets = [];
        $this->aliases = [];

        Log::debug("APEX: Cleared all registered widgets");

        return $this;
    }
}
