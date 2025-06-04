<?php

namespace App\Apex\Core\Widget;

class WidgetRenderer
{
    protected WidgetRegistry $registry;

    public function __construct(WidgetRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Render a widget from JSON configuration
     */
    public function render(array $widgetConfig): array
    {
        $type = $widgetConfig['type'] ?? null;

        if (!$type) {
            throw new \InvalidArgumentException('Widget type is required');
        }

        $widget = $this->registry->create($type, $widgetConfig);

        return [
            'id' => $widget->getId(),
            'type' => $widget->getType(),
            'props' => $widget->transform($widgetConfig),
        ];
    }

    /**
     * Render multiple widgets
     */
    public function renderMany(array $widgets): array
    {
        return array_map([$this, 'render'], $widgets);
    }
}
