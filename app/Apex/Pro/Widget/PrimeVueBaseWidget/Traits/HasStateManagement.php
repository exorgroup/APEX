<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: State management trait for PRO widgets providing hybrid client/server state synchronization with conflict resolution and real-time updates
 * File location: app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits/HasStateManagement.php
 */

namespace App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits;

use App\Apex\Pro\Widget\PrimeVueBaseWidget\Services\StateManager;
use Illuminate\Support\Facades\Log;

trait HasStateManagement
{
    /**
     * State configuration storage
     * @var array
     */
    protected array $stateConfig = [];

    /**
     * State manager instance
     * @var StateManager|null
     */
    protected ?StateManager $stateManager = null;

    /**
     * Current widget state
     * @var array
     */
    protected array $currentState = [];

    /**
     * State synchronization settings
     * @var array
     */
    protected array $syncSettings = [
        'syncToServer' => true,
        'localState' => true,
        'conflictResolution' => 'server',
        'autoSync' => true,
        'syncInterval' => 5000,
        'batchUpdates' => true
    ];

    /**
     * Initialize state management system
     * @param array $config Widget configuration containing state definitions
     * @return void
     */
    protected function initializeStateManagement(array $config): void
    {
        try {
            if (isset($config['stateConfig'])) {
                $this->stateConfig = $config['stateConfig'];
                $this->syncSettings = array_merge($this->syncSettings, $this->stateConfig);
                $this->stateManager = new StateManager($this->syncSettings);
                $this->initializeWidgetState($config);
            }
        } catch (\Exception $e) {
            Log::error('Error initializing state management', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'initializeStateManagement',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get state schema for widget validation
     * @return array JSON schema definition for state management
     */
    protected function getStateSchema(): array
    {
        try {
            return [
                'type' => 'object',
                'description' => 'State management configuration',
                'properties' => [
                    'syncToServer' => [
                        'type' => 'boolean',
                        'description' => 'Enable server synchronization',
                        'default' => true
                    ],
                    'localState' => [
                        'type' => 'boolean',
                        'description' => 'Maintain local state for performance',
                        'default' => true
                    ],
                    'conflictResolution' => [
                        'type' => 'string',
                        'description' => 'Conflict resolution strategy',
                        'enum' => ['client', 'server', 'merge', 'prompt'],
                        'default' => 'server'
                    ],
                    'autoSync' => [
                        'type' => 'boolean',
                        'description' => 'Automatically sync state changes',
                        'default' => true
                    ],
                    'syncInterval' => [
                        'type' => 'integer',
                        'description' => 'Sync interval in milliseconds',
                        'default' => 5000,
                        'minimum' => 1000
                    ],
                    'batchUpdates' => [
                        'type' => 'boolean',
                        'description' => 'Batch multiple updates into single sync',
                        'default' => true
                    ],
                    'persistState' => [
                        'type' => 'boolean',
                        'description' => 'Persist state across sessions',
                        'default' => false
                    ],
                    'stateValidation' => [
                        'type' => 'object',
                        'description' => 'State validation rules',
                        'properties' => [
                            'required' => [
                                'type' => 'array',
                                'description' => 'Required state properties',
                                'items' => ['type' => 'string']
                            ],
                            'types' => [
                                'type' => 'object',
                                'description' => 'State property type validation'
                            ]
                        ]
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting state schema', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'getStateSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Process state configuration and setup management
     * @param array $stateConfig Raw state configuration from JSON
     * @return array Processed state configuration
     */
    protected function processStateConfig(array $stateConfig): array
    {
        try {
            $processedConfig = [
                'syncToServer' => $stateConfig['syncToServer'] ?? true,
                'localState' => $stateConfig['localState'] ?? true,
                'conflictResolution' => $this->validateConflictResolution($stateConfig['conflictResolution'] ?? 'server'),
                'autoSync' => $stateConfig['autoSync'] ?? true,
                'syncInterval' => max(1000, $stateConfig['syncInterval'] ?? 5000),
                'batchUpdates' => $stateConfig['batchUpdates'] ?? true,
                'persistState' => $stateConfig['persistState'] ?? false,
                'stateValidation' => $stateConfig['stateValidation'] ?? [],
                'compression' => $stateConfig['compression'] ?? false,
                'encryption' => $stateConfig['encryption'] ?? false
            ];

            return $processedConfig;
        } catch (\Exception $e) {
            Log::error('Error processing state configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'processStateConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $stateConfig;
        }
    }

    /**
     * Initialize widget state from configuration
     * @param array $config Widget configuration
     * @return void
     */
    protected function initializeWidgetState(array $config): void
    {
        try {
            // Set initial state
            $this->currentState = [
                'widgetId' => $config['id'] ?? uniqid('widget_'),
                'value' => $config['value'] ?? null,
                'valid' => true,
                'dirty' => false,
                'touched' => false,
                'focused' => false,
                'loading' => false,
                'lastUpdated' => now()->toISOString(),
                'version' => 1,
                'syncStatus' => 'synchronized'
            ];

            // Add custom state properties if defined
            if (isset($config['initialState'])) {
                $this->currentState = array_merge($this->currentState, $config['initialState']);
            }

            // Register state with manager
            if ($this->stateManager) {
                $this->stateManager->registerWidget($this->currentState['widgetId'], $this->currentState);
            }
        } catch (\Exception $e) {
            Log::error('Error initializing widget state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'initializeWidgetState',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Update widget state
     * @param array $stateUpdates State properties to update
     * @param bool $syncToServer Whether to sync changes to server
     * @return bool Success status
     */
    public function updateState(array $stateUpdates, bool $syncToServer = null): bool
    {
        try {
            $syncToServer = $syncToServer ?? $this->syncSettings['syncToServer'];

            // Validate state updates
            if (!$this->validateStateUpdates($stateUpdates)) {
                return false;
            }

            // Merge updates with current state
            $previousState = $this->currentState;
            $this->currentState = array_merge($this->currentState, $stateUpdates);

            // Update metadata
            $this->currentState['lastUpdated'] = now()->toISOString();
            $this->currentState['version']++;
            $this->currentState['dirty'] = true;

            // Check for conflicts if server sync is enabled
            if ($syncToServer && $this->stateManager) {
                $conflictResult = $this->stateManager->checkForConflicts(
                    $this->currentState['widgetId'],
                    $this->currentState
                );

                if ($conflictResult['hasConflict']) {
                    return $this->resolveStateConflict($conflictResult, $previousState);
                }

                // Sync to server if auto-sync is enabled
                if ($this->syncSettings['autoSync']) {
                    $this->syncStateToServer();
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'updateState',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get current widget state
     * @param array $properties Specific properties to retrieve (empty for all)
     * @return array Current state or specific properties
     */
    public function getState(array $properties = []): array
    {
        try {
            if (empty($properties)) {
                return $this->currentState;
            }

            $filteredState = [];
            foreach ($properties as $property) {
                if (isset($this->currentState[$property])) {
                    $filteredState[$property] = $this->currentState[$property];
                }
            }

            return $filteredState;
        } catch (\Exception $e) {
            Log::error('Error getting state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'getState',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Sync state to server
     * @return bool Success status
     */
    public function syncStateToServer(): bool
    {
        try {
            if (!$this->stateManager || !$this->syncSettings['syncToServer']) {
                return false;
            }

            $result = $this->stateManager->syncToServer(
                $this->currentState['widgetId'],
                $this->currentState
            );

            if ($result['success']) {
                $this->currentState['syncStatus'] = 'synchronized';
                $this->currentState['dirty'] = false;

                // Update with server response if provided
                if (isset($result['serverState'])) {
                    $this->mergeServerState($result['serverState']);
                }
            } else {
                $this->currentState['syncStatus'] = 'error';
                Log::warning('State sync to server failed', [
                    'widgetId' => $this->currentState['widgetId'],
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }

            return $result['success'];
        } catch (\Exception $e) {
            Log::error('Error syncing state to server', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'syncStateToServer',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Sync state from server
     * @return bool Success status
     */
    public function syncStateFromServer(): bool
    {
        try {
            if (!$this->stateManager || !$this->syncSettings['syncToServer']) {
                return false;
            }

            $result = $this->stateManager->syncFromServer($this->currentState['widgetId']);

            if ($result['success'] && isset($result['serverState'])) {
                $this->mergeServerState($result['serverState']);
                $this->currentState['syncStatus'] = 'synchronized';
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error syncing state from server', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'syncStateFromServer',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Validate state updates against schema
     * @param array $stateUpdates State updates to validate
     * @return bool Validation result
     */
    protected function validateStateUpdates(array $stateUpdates): bool
    {
        try {
            // Check required properties
            if (isset($this->stateConfig['stateValidation']['required'])) {
                foreach ($this->stateConfig['stateValidation']['required'] as $required) {
                    if (!isset($stateUpdates[$required]) && !isset($this->currentState[$required])) {
                        Log::warning('Required state property missing', [
                            'property' => $required,
                            'widgetId' => $this->currentState['widgetId'] ?? 'unknown'
                        ]);
                        return false;
                    }
                }
            }

            // Check property types
            if (isset($this->stateConfig['stateValidation']['types'])) {
                foreach ($this->stateConfig['stateValidation']['types'] as $property => $expectedType) {
                    if (isset($stateUpdates[$property])) {
                        if (!$this->validatePropertyType($stateUpdates[$property], $expectedType)) {
                            Log::warning('Invalid state property type', [
                                'property' => $property,
                                'expectedType' => $expectedType,
                                'actualType' => gettype($stateUpdates[$property]),
                                'widgetId' => $this->currentState['widgetId'] ?? 'unknown'
                            ]);
                            return false;
                        }
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error validating state updates', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'validateStateUpdates',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Validate property type
     * @param mixed $value Value to validate
     * @param string $expectedType Expected type
     * @return bool Validation result
     */
    protected function validatePropertyType(mixed $value, string $expectedType): bool
    {
        try {
            return match ($expectedType) {
                'string' => is_string($value),
                'integer', 'int' => is_int($value),
                'float', 'double' => is_float($value),
                'boolean', 'bool' => is_bool($value),
                'array' => is_array($value),
                'object' => is_object($value),
                'null' => is_null($value),
                default => true // Unknown type, allow it
            };
        } catch (\Exception $e) {
            Log::error('Error validating property type', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'validatePropertyType',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Resolve state conflict based on strategy
     * @param array $conflictResult Conflict detection result
     * @param array $previousState Previous state before conflict
     * @return bool Resolution success
     */
    protected function resolveStateConflict(array $conflictResult, array $previousState): bool
    {
        try {
            $strategy = $this->syncSettings['conflictResolution'];

            switch ($strategy) {
                case 'client':
                    // Client wins - keep current state
                    return true;

                case 'server':
                    // Server wins - use server state
                    $this->currentState = $conflictResult['serverState'];
                    return true;

                case 'merge':
                    // Merge states - server values take precedence for conflicts
                    $this->currentState = array_merge($this->currentState, $conflictResult['serverState']);
                    return true;

                case 'prompt':
                    // Prompt user - for now, default to server
                    $this->currentState = $conflictResult['serverState'];
                    Log::info('State conflict resolved via prompt (defaulted to server)', [
                        'widgetId' => $this->currentState['widgetId']
                    ]);
                    return true;

                default:
                    // Unknown strategy - default to server
                    $this->currentState = $conflictResult['serverState'];
                    return true;
            }
        } catch (\Exception $e) {
            Log::error('Error resolving state conflict', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'resolveStateConflict',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Merge server state with current state
     * @param array $serverState Server state to merge
     * @return void
     */
    protected function mergeServerState(array $serverState): void
    {
        try {
            // Preserve local-only properties
            $localProperties = ['focused', 'touched', 'loading'];
            $preservedProperties = [];

            foreach ($localProperties as $property) {
                if (isset($this->currentState[$property])) {
                    $preservedProperties[$property] = $this->currentState[$property];
                }
            }

            // Merge server state
            $this->currentState = array_merge($this->currentState, $serverState);

            // Restore local properties
            $this->currentState = array_merge($this->currentState, $preservedProperties);

            // Update sync metadata
            $this->currentState['lastServerSync'] = now()->toISOString();
        } catch (\Exception $e) {
            Log::error('Error merging server state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'mergeServerState',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Validate conflict resolution strategy
     * @param string $strategy Strategy to validate
     * @return string Valid strategy
     */
    protected function validateConflictResolution(string $strategy): string
    {
        try {
            $validStrategies = ['client', 'server', 'merge', 'prompt'];

            if (in_array($strategy, $validStrategies)) {
                return $strategy;
            }

            Log::warning('Invalid conflict resolution strategy, defaulting to server', [
                'invalidStrategy' => $strategy,
                'validStrategies' => $validStrategies
            ]);

            return 'server';
        } catch (\Exception $e) {
            Log::error('Error validating conflict resolution', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'validateConflictResolution',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'server';
        }
    }

    /**
     * Check if widget has state management capability
     * @return bool True if state management is available
     */
    protected function hasStateManagement(): bool
    {
        try {
            return !empty($this->stateConfig) && $this->stateManager !== null;
        } catch (\Exception $e) {
            Log::error('Error checking state management capability', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'hasStateManagement',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get current state configuration
     * @return array Current state configuration
     */
    public function getStateConfig(): array
    {
        try {
            return $this->stateConfig;
        } catch (\Exception $e) {
            Log::error('Error getting state configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'getStateConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Reset widget state to initial values
     * @return bool Success status
     */
    public function resetState(): bool
    {
        try {
            if ($this->stateManager) {
                return $this->stateManager->resetWidget($this->currentState['widgetId']);
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error resetting state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasStateManagement.php',
                'method' => 'resetState',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
