<?php

namespace ExorGroup\Apex\Support;

use Illuminate\Support\Facades\Log;

class WidgetConnectionManager
{
    /**
     * Widget connections
     */
    protected array $connections = [];

    /**
     * Bidirectional connections
     */
    protected array $bidirectionalConnections = [];

    /**
     * Event transformations
     */
    protected array $transformations = [];

    /**
     * Register a connection between widgets
     */
    public function connect(string $sourceId, string $targetId, array $options = []): void
    {
        $bidirectional = $options['bidirectional'] ?? false;
        $eventMap = $options['eventMap'] ?? null;
        $transforms = $options['transforms'] ?? [];

        // Add source -> target connection
        $this->addConnection($sourceId, $targetId, $eventMap, $transforms);

        // If bidirectional, add target -> source connection
        if ($bidirectional) {
            $inverseEventMap = $this->createInverseEventMap($eventMap);
            $inverseTransforms = $this->createInverseTransforms($transforms);

            $this->addConnection($targetId, $sourceId, $inverseEventMap, $inverseTransforms);

            // Track bidirectional connection
            $this->bidirectionalConnections[] = [
                'source' => $sourceId,
                'target' => $targetId,
                'created_at' => microtime(true)
            ];
        }

        Log::debug("APEX: Created widget connection", [
            'source' => $sourceId,
            'target' => $targetId,
            'bidirectional' => $bidirectional,
            'has_transforms' => !empty($transforms)
        ]);
    }

    /**
     * Add a one-way connection
     */
    protected function addConnection(string $sourceId, string $targetId, ?array $eventMap = null, array $transforms = []): void
    {
        if (!isset($this->connections[$sourceId])) {
            $this->connections[$sourceId] = [];
        }

        $this->connections[$sourceId][] = [
            'target' => $targetId,
            'eventMap' => $eventMap,
            'transforms' => $transforms,
            'created_at' => microtime(true)
        ];

        // Store transformations separately for easy access
        if (!empty($transforms)) {
            if (!isset($this->transformations[$sourceId])) {
                $this->transformations[$sourceId] = [];
            }

            $this->transformations[$sourceId][$targetId] = $transforms;
        }
    }

    /**
     * Create an inverse event map for bidirectional connections
     */
    protected function createInverseEventMap(?array $eventMap): ?array
    {
        if (!$eventMap) {
            return null;
        }

        $inverse = [];

        foreach ($eventMap as $sourceEvent => $targetEvent) {
            // Handle complex event configurations
            if (is_array($targetEvent) && isset($targetEvent['inverse'])) {
                $inverse[$targetEvent['event']] = [
                    'event' => $sourceEvent,
                    'transform' => $targetEvent['inverse_transform'] ?? null
                ];
            }
            // Simple string mapping
            elseif (is_string($targetEvent)) {
                $inverse[$targetEvent] = $sourceEvent;
            }
            // Array without inverse
            elseif (is_array($targetEvent) && isset($targetEvent['event'])) {
                $inverse[$targetEvent['event']] = $sourceEvent;
            }
        }

        return $inverse;
    }

    /**
     * Create inverse transformations
     */
    protected function createInverseTransforms(array $transforms): array
    {
        $inverse = [];

        foreach ($transforms as $event => $transform) {
            if (is_array($transform) && isset($transform['inverse'])) {
                $inverse[$event] = $transform['inverse'];
            }
        }

        return $inverse;
    }

    /**
     * Generate JavaScript to set up connections
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
            foreach ($targets as $connection) {
                $targetId = $connection['target'];
                $eventMap = $connection['eventMap'];
                $transforms = $connection['transforms'];

                $script .= "    // Connect {$sourceId} to {$targetId}\n";

                // Build connection parameters
                $connectionParams = [];

                if ($eventMap) {
                    $connectionParams[] = json_encode($eventMap);
                } else {
                    $connectionParams[] = 'null';
                }

                if (!empty($transforms)) {
                    $connectionParams[] = json_encode($transforms);
                }

                // Build the parameters string to be inserted into the JavaScript
                $paramsStringForJs = implode(', ', $connectionParams);

                // Corrected line: Concatenate the conditional part
                $script .= "    window.ApexWidgetManager.connectWidgets('{$sourceId}', '{$targetId}'" .
                    ($paramsStringForJs ? ', ' . $paramsStringForJs : '') . ");\n";
            }
        }

        // Add transformation registration if any exist
        if (!empty($this->transformations)) {
            $script .= "\n    // Register transformations\n";
            foreach ($this->transformations as $sourceId => $targets) {
                foreach ($targets as $targetId => $transforms) {
                    foreach ($transforms as $event => $transform) {
                        $transformJson = json_encode($transform);
                        $script .= "    window.ApexWidgetManager.registerTransform('{$sourceId}', '{$targetId}', '{$event}', {$transformJson});\n";
                    }
                }
            }
        }

        $script .= "});\n";

        return $script;
    }

    /**
     * Get all connections for a widget
     */
    public function getConnections(string $widgetId): array
    {
        return $this->connections[$widgetId] ?? [];
    }

    /**
     * Get all incoming connections to a widget
     */
    public function getIncomingConnections(string $widgetId): array
    {
        $incoming = [];

        foreach ($this->connections as $sourceId => $targets) {
            foreach ($targets as $connection) {
                if ($connection['target'] === $widgetId) {
                    $incoming[] = [
                        'source' => $sourceId,
                        'connection' => $connection
                    ];
                }
            }
        }

        return $incoming;
    }

    /**
     * Check if two widgets are connected
     */
    public function areConnected(string $sourceId, string $targetId): bool
    {
        $connections = $this->getConnections($sourceId);

        foreach ($connections as $connection) {
            if ($connection['target'] === $targetId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if two widgets are bidirectionally connected
     */
    public function areBidirectionallyConnected(string $widgetId1, string $widgetId2): bool
    {
        foreach ($this->bidirectionalConnections as $connection) {
            if (($connection['source'] === $widgetId1 && $connection['target'] === $widgetId2) ||
                ($connection['source'] === $widgetId2 && $connection['target'] === $widgetId1)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove a connection
     */
    public function disconnect(string $sourceId, string $targetId): void
    {
        if (isset($this->connections[$sourceId])) {
            $this->connections[$sourceId] = array_filter($this->connections[$sourceId], function ($connection) use ($targetId) {
                return $connection['target'] !== $targetId;
            });

            // Remove source entry if no connections remain
            if (empty($this->connections[$sourceId])) {
                unset($this->connections[$sourceId]);
            }
        }

        // Remove transformations
        if (isset($this->transformations[$sourceId][$targetId])) {
            unset($this->transformations[$sourceId][$targetId]);

            if (empty($this->transformations[$sourceId])) {
                unset($this->transformations[$sourceId]);
            }
        }

        // Remove from bidirectional connections
        $this->bidirectionalConnections = array_filter($this->bidirectionalConnections, function ($connection) use ($sourceId, $targetId) {
            return !($connection['source'] === $sourceId && $connection['target'] === $targetId);
        });

        Log::debug("APEX: Removed widget connection", [
            'source' => $sourceId,
            'target' => $targetId
        ]);
    }

    /**
     * Remove all connections for a widget
     */
    public function disconnectWidget(string $widgetId): void
    {
        // Remove outgoing connections
        unset($this->connections[$widgetId]);
        unset($this->transformations[$widgetId]);

        // Remove incoming connections
        foreach ($this->connections as $sourceId => &$targets) {
            $targets = array_filter($targets, function ($connection) use ($widgetId) {
                return $connection['target'] !== $widgetId;
            });
        }

        // Remove from transformations as target
        foreach ($this->transformations as $sourceId => &$targets) {
            unset($targets[$widgetId]);
        }

        // Remove from bidirectional connections
        $this->bidirectionalConnections = array_filter($this->bidirectionalConnections, function ($connection) use ($widgetId) {
            return $connection['source'] !== $widgetId && $connection['target'] !== $widgetId;
        });

        Log::debug("APEX: Disconnected all connections for widget", ['widget' => $widgetId]);
    }

    /**
     * Get connection statistics
     */
    public function getConnectionStats(): array
    {
        $totalConnections = 0;
        $totalTransformations = 0;

        foreach ($this->connections as $targets) {
            $totalConnections += count($targets);
        }

        foreach ($this->transformations as $targets) {
            foreach ($targets as $transforms) {
                $totalTransformations += count($transforms);
            }
        }

        return [
            'total_connections' => $totalConnections,
            'bidirectional_connections' => count($this->bidirectionalConnections),
            'total_transformations' => $totalTransformations,
            'widgets_with_outgoing' => count($this->connections),
            'widgets_with_transformations' => count($this->transformations)
        ];
    }

    /**
     * Get all connections
     */
    public function getAllConnections(): array
    {
        return $this->connections;
    }

    /**
     * Get all bidirectional connections
     */
    public function getBidirectionalConnections(): array
    {
        return $this->bidirectionalConnections;
    }

    /**
     * Get all transformations
     */
    public function getTransformations(): array
    {
        return $this->transformations;
    }

    /**
     * Clear all connections (for testing or cleanup)
     */
    public function clear(): void
    {
        $this->connections = [];
        $this->bidirectionalConnections = [];
        $this->transformations = [];

        Log::debug("APEX: Cleared all widget connections");
    }
}
