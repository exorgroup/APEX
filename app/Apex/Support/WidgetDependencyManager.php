<?php

namespace App\Apex\Support;

use Illuminate\Support\Facades\Log;

class WidgetDependencyManager
{
    /**
     * List of registered widgets and their dependencies
     */
    protected array $widgets = [];

    /**
     * Connection mappings between widgets
     */
    protected array $connections = [];

    /**
     * Register a widget with possible dependencies
     */
    public function register(string $id, string $type, array $targets = []): void
    {
        $this->widgets[$id] = [
            'type' => $type,
            'targets' => $targets,
            'registered_at' => microtime(true)
        ];

        // Create connections for each target
        foreach ($targets as $targetId) {
            $this->addConnection($id, $targetId);
        }

        Log::debug("APEX: Registered widget dependency", [
            'id' => $id,
            'type' => $type,
            'targets' => $targets
        ]);
    }

    /**
     * Add a connection between two widgets
     */
    public function addConnection(string $sourceId, string $targetId, array $options = []): void
    {
        if (!isset($this->connections[$sourceId])) {
            $this->connections[$sourceId] = [];
        }

        $this->connections[$sourceId][] = [
            'target' => $targetId,
            'options' => $options,
            'created_at' => microtime(true)
        ];
    }

    /**
     * Check if all target widgets exist
     */
    public function validateDependencies(): array
    {
        $missingDependencies = [];

        foreach ($this->widgets as $id => $widget) {
            foreach ($widget['targets'] as $targetId) {
                if (!isset($this->widgets[$targetId])) {
                    $missingDependencies[] = [
                        'widget' => $id,
                        'target' => $targetId,
                        'widget_type' => $widget['type']
                    ];
                }
            }
        }

        return $missingDependencies;
    }

    /**
     * Generate JavaScript to automatically wire up widget dependencies
     */
    public function generateConnectionScript(): string
    {
        if (empty($this->connections)) {
            return '';
        }

        $script = "// APEX Widget Connections\n";
        $script .= "document.addEventListener('DOMContentLoaded', function() {\n";
        $script .= "    if (!window.ApexWidgetManager) {\n";
        $script .= "        console.warn('APEX: ApexWidgetManager not found. Widget connections will not work.');\n";
        $script .= "        return;\n";
        $script .= "    }\n\n";

        foreach ($this->connections as $sourceId => $targets) {
            $sourceWidget = $this->widgets[$sourceId] ?? null;

            if (!$sourceWidget) {
                continue;
            }

            foreach ($targets as $connection) {
                $targetId = $connection['target'];
                $targetWidget = $this->widgets[$targetId] ?? null;

                if (!$targetWidget) {
                    $script .= "    // Skipping connection {$sourceId} -> {$targetId} (target not found)\n";
                    continue;
                }

                $script .= "    // Connect {$sourceId} ({$sourceWidget['type']}) to {$targetId} ({$targetWidget['type']})\n";

                // Generate connection with automatic event mapping
                $eventMap = $this->generateEventMap($sourceWidget['type'], $targetWidget['type']);

                if (!empty($eventMap)) {
                    $eventMapJson = json_encode($eventMap);
                    $script .= "    window.ApexWidgetManager.connectWidgets('{$sourceId}', '{$targetId}', {$eventMapJson});\n";
                } else {
                    $script .= "    window.ApexWidgetManager.connectWidgets('{$sourceId}', '{$targetId}');\n";
                }
            }
        }

        $script .= "});\n";

        return $script;
    }

    /**
     * Generate default event mapping based on widget types
     */
    protected function generateEventMap(string $sourceType, string $targetType): array
    {
        // Common widget interaction patterns
        $patterns = [
            'dataFilter' => [
                'dataTable' => [
                    'filter:applied' => 'filter:applied',
                    'filter:reset' => 'filter:reset'
                ],
                'chart' => [
                    'filter:applied' => 'filter:applied',
                    'filter:reset' => 'filter:reset'
                ]
            ],
            'dateRangePicker' => [
                'dataTable' => [
                    'dateRange:changed' => 'dateRange:changed'
                ],
                'chart' => [
                    'dateRange:changed' => 'dateRange:changed'
                ],
                'dashboard' => [
                    'dateRange:changed' => 'dateRange:changed'
                ]
            ],
            'dataTable' => [
                'detailView' => [
                    'record:selected' => 'record:load'
                ],
                'chart' => [
                    'record:selected' => 'highlight:record'
                ]
            ],
            'controlPanel' => [
                'dataTable' => [
                    'command:refresh' => 'refresh',
                    'command:export' => 'export'
                ],
                'form' => [
                    'command:clear' => 'clear',
                    'command:submit' => 'submit'
                ]
            ]
        ];

        return $patterns[$sourceType][$targetType] ?? [];
    }

    /**
     * Get all registered widgets
     */
    public function getRegisteredWidgets(): array
    {
        return $this->widgets;
    }

    /**
     * Get all connections
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * Get widgets by type
     */
    public function getWidgetsByType(string $type): array
    {
        return array_filter($this->widgets, function ($widget) use ($type) {
            return $widget['type'] === $type;
        });
    }

    /**
     * Check if a widget exists
     */
    public function hasWidget(string $id): bool
    {
        return isset($this->widgets[$id]);
    }

    /**
     * Get widget information
     */
    public function getWidget(string $id): ?array
    {
        return $this->widgets[$id] ?? null;
    }

    /**
     * Clear all dependencies (for testing or cleanup)
     */
    public function clear(): void
    {
        $this->widgets = [];
        $this->connections = [];

        Log::debug("APEX: Cleared all widget dependencies");
    }

    /**
     * Remove a specific widget and its connections
     */
    public function removeWidget(string $id): void
    {
        // Remove widget
        unset($this->widgets[$id]);

        // Remove connections where this widget is the source
        unset($this->connections[$id]);

        // Remove connections where this widget is the target
        foreach ($this->connections as $sourceId => &$targets) {
            $targets = array_filter($targets, function ($connection) use ($id) {
                return $connection['target'] !== $id;
            });
        }

        Log::debug("APEX: Removed widget dependency", ['id' => $id]);
    }

    /**
     * Get dependency statistics (for debugging)
     */
    public function getStats(): array
    {
        $totalConnections = 0;
        foreach ($this->connections as $targets) {
            $totalConnections += count($targets);
        }

        $widgetTypes = [];
        foreach ($this->widgets as $widget) {
            $type = $widget['type'];
            $widgetTypes[$type] = ($widgetTypes[$type] ?? 0) + 1;
        }

        return [
            'total_widgets' => count($this->widgets),
            'total_connections' => $totalConnections,
            'widget_types' => $widgetTypes,
            'avg_connections_per_widget' => count($this->widgets) > 0 ? round($totalConnections / count($this->widgets), 2) : 0
        ];
    }
}
