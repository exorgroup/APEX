<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: State manager service for PRO widgets providing server-side state coordination, conflict detection, persistence and synchronization management for hybrid client/server state
 * File location: app/Apex/Pro/Widget/PrimeVueBaseWidget/Services/StateManager.php
 */

namespace App\Apex\Pro\Widget\PrimeVueBaseWidget\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StateManager
{
    /**
     * Registered widget states
     * @var array
     */
    protected array $widgetStates = [];

    /**
     * State persistence storage
     * @var array
     */
    protected array $persistedStates = [];

    /**
     * Configuration settings
     * @var array
     */
    protected array $config = [
        'syncToServer' => true,
        'localState' => true,
        'conflictResolution' => 'server',
        'autoSync' => true,
        'syncInterval' => 5000,
        'batchUpdates' => true,
        'persistState' => false,
        'enableCaching' => true,
        'cacheDuration' => 3600,
        'enableDatabase' => false,
        'tableName' => 'widget_states',
        'maxStateHistory' => 50,
        'enableCompression' => false,
        'enableEncryption' => false
    ];

    /**
     * State change history
     * @var array
     */
    protected array $stateHistory = [];

    /**
     * Conflict resolution strategies
     * @var array
     */
    protected array $conflictStrategies = [
        'client' => 'Client state takes precedence',
        'server' => 'Server state takes precedence',
        'merge' => 'Merge states with server priority',
        'prompt' => 'Prompt user for resolution'
    ];

    /**
     * Constructor
     * @param array $config Configuration options
     */
    public function __construct(array $config = [])
    {
        try {
            $this->config = array_merge($this->config, $config);
            $this->initializeStateManager();
        } catch (\Exception $e) {
            Log::error('Error initializing StateManager', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => '__construct',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Register widget with state manager
     * @param string $widgetId Widget identifier
     * @param array $initialState Initial widget state
     * @return bool Success status
     */
    public function registerWidget(string $widgetId, array $initialState): bool
    {
        try {
            if (empty($widgetId)) {
                Log::warning('Invalid widget ID provided for registration');
                return false;
            }

            // Validate initial state
            $validatedState = $this->validateState($initialState);
            if (!$validatedState) {
                Log::warning('Invalid initial state provided', [
                    'widgetId' => $widgetId,
                    'state' => $initialState
                ]);
                return false;
            }

            // Set default state properties
            $state = array_merge([
                'widgetId' => $widgetId,
                'value' => null,
                'valid' => true,
                'dirty' => false,
                'touched' => false,
                'focused' => false,
                'loading' => false,
                'lastUpdated' => now()->toISOString(),
                'version' => 1,
                'syncStatus' => 'synchronized',
                'createdAt' => now()->toISOString(),
                'userId' => Auth::id()
            ], $validatedState);

            // Register widget state
            $this->widgetStates[$widgetId] = $state;

            // Persist if enabled
            if ($this->config['persistState']) {
                $this->persistWidgetState($widgetId, $state);
            }

            // Cache if enabled
            if ($this->config['enableCaching']) {
                $this->cacheWidgetState($widgetId, $state);
            }

            // Add to history
            $this->addStateHistory($widgetId, 'registered', null, $state);

            Log::info('Widget registered with state manager', [
                'widgetId' => $widgetId,
                'initialStateKeys' => array_keys($state)
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error registering widget', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'registerWidget',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Update widget state
     * @param string $widgetId Widget identifier
     * @param array $stateUpdates State updates to apply
     * @return array Update result with success status and conflicts
     */
    public function updateState(string $widgetId, array $stateUpdates): array
    {
        try {
            if (!isset($this->widgetStates[$widgetId])) {
                return [
                    'success' => false,
                    'error' => 'Widget not registered',
                    'widgetId' => $widgetId
                ];
            }

            $previousState = $this->widgetStates[$widgetId];
            $newState = array_merge($previousState, $stateUpdates);

            // Validate state updates
            if (!$this->validateState($newState)) {
                return [
                    'success' => false,
                    'error' => 'Invalid state updates',
                    'widgetId' => $widgetId
                ];
            }

            // Update metadata
            $newState['lastUpdated'] = now()->toISOString();
            $newState['version']++;
            $newState['dirty'] = true;

            // Check for conflicts
            $conflict = $this->detectConflicts($widgetId, $newState);
            if ($conflict['hasConflict']) {
                return [
                    'success' => false,
                    'conflict' => true,
                    'conflictData' => $conflict,
                    'widgetId' => $widgetId
                ];
            }

            // Apply state update
            $this->widgetStates[$widgetId] = $newState;

            // Persist if enabled
            if ($this->config['persistState']) {
                $this->persistWidgetState($widgetId, $newState);
            }

            // Update cache
            if ($this->config['enableCaching']) {
                $this->cacheWidgetState($widgetId, $newState);
            }

            // Add to history
            $this->addStateHistory($widgetId, 'updated', $previousState, $newState);

            return [
                'success' => true,
                'previousState' => $previousState,
                'newState' => $newState,
                'widgetId' => $widgetId
            ];
        } catch (\Exception $e) {
            Log::error('Error updating widget state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'updateState',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'widgetId' => $widgetId
            ];
        }
    }

    /**
     * Get widget state
     * @param string $widgetId Widget identifier
     * @param bool $includeHistory Whether to include state history
     * @return array|null Widget state or null if not found
     */
    public function getState(string $widgetId, bool $includeHistory = false): ?array
    {
        try {
            // Check memory first
            if (isset($this->widgetStates[$widgetId])) {
                $state = $this->widgetStates[$widgetId];

                if ($includeHistory) {
                    $state['history'] = $this->getStateHistory($widgetId);
                }

                return $state;
            }

            // Check cache
            if ($this->config['enableCaching']) {
                $cached = $this->getCachedState($widgetId);
                if ($cached) {
                    $this->widgetStates[$widgetId] = $cached;
                    return $cached;
                }
            }

            // Check persistent storage
            if ($this->config['persistState']) {
                $persisted = $this->loadPersistedState($widgetId);
                if ($persisted) {
                    $this->widgetStates[$widgetId] = $persisted;
                    return $persisted;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting widget state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'getState',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Detect state conflicts
     * @param string $widgetId Widget identifier
     * @param array $newState New state to check
     * @return array Conflict detection result
     */
    public function checkForConflicts(string $widgetId, array $newState): array
    {
        try {
            return $this->detectConflicts($widgetId, $newState);
        } catch (\Exception $e) {
            Log::error('Error checking for conflicts', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'checkForConflicts',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['hasConflict' => false];
        }
    }

    /**
     * Sync state to server (mock implementation)
     * @param string $widgetId Widget identifier
     * @param array $state State to sync
     * @return array Sync result
     */
    public function syncToServer(string $widgetId, array $state): array
    {
        try {
            // This is a mock implementation
            // In a real implementation, this would make an API call to sync state

            // Simulate server processing
            usleep(100000); // 100ms delay

            // Update sync status
            $state['syncStatus'] = 'synchronized';
            $state['lastServerSync'] = now()->toISOString();
            $state['dirty'] = false;

            // Update local state
            $this->widgetStates[$widgetId] = $state;

            // Persist changes
            if ($this->config['persistState']) {
                $this->persistWidgetState($widgetId, $state);
            }

            // Update cache
            if ($this->config['enableCaching']) {
                $this->cacheWidgetState($widgetId, $state);
            }

            Log::info('Widget state synced to server', [
                'widgetId' => $widgetId,
                'version' => $state['version']
            ]);

            return [
                'success' => true,
                'serverState' => $state,
                'widgetId' => $widgetId,
                'syncTime' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Error syncing state to server', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'syncToServer',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'widgetId' => $widgetId
            ];
        }
    }

    /**
     * Sync state from server (mock implementation)
     * @param string $widgetId Widget identifier
     * @return array Sync result
     */
    public function syncFromServer(string $widgetId): array
    {
        try {
            // This is a mock implementation
            // In a real implementation, this would make an API call to fetch state

            $currentState = $this->getState($widgetId);
            if (!$currentState) {
                return [
                    'success' => false,
                    'error' => 'Widget not found',
                    'widgetId' => $widgetId
                ];
            }

            // Simulate server response
            usleep(50000); // 50ms delay

            // For mock purposes, return current state as "server state"
            $serverState = $currentState;
            $serverState['lastServerSync'] = now()->toISOString();

            Log::info('Widget state synced from server', [
                'widgetId' => $widgetId,
                'version' => $serverState['version']
            ]);

            return [
                'success' => true,
                'serverState' => $serverState,
                'widgetId' => $widgetId,
                'syncTime' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Error syncing state from server', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'syncFromServer',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'widgetId' => $widgetId
            ];
        }
    }

    /**
     * Reset widget to initial state
     * @param string $widgetId Widget identifier
     * @return bool Success status
     */
    public function resetWidget(string $widgetId): bool
    {
        try {
            if (!isset($this->widgetStates[$widgetId])) {
                return false;
            }

            $currentState = $this->widgetStates[$widgetId];

            // Reset to initial state
            $resetState = [
                'widgetId' => $widgetId,
                'value' => null,
                'valid' => true,
                'dirty' => false,
                'touched' => false,
                'focused' => false,
                'loading' => false,
                'lastUpdated' => now()->toISOString(),
                'version' => 1,
                'syncStatus' => 'synchronized',
                'resetAt' => now()->toISOString(),
                'userId' => $currentState['userId'] ?? Auth::id()
            ];

            $this->widgetStates[$widgetId] = $resetState;

            // Persist if enabled
            if ($this->config['persistState']) {
                $this->persistWidgetState($widgetId, $resetState);
            }

            // Update cache
            if ($this->config['enableCaching']) {
                $this->cacheWidgetState($widgetId, $resetState);
            }

            // Add to history
            $this->addStateHistory($widgetId, 'reset', $currentState, $resetState);

            Log::info('Widget state reset', [
                'widgetId' => $widgetId
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error resetting widget state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'resetWidget',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Detect state conflicts
     * @param string $widgetId Widget identifier
     * @param array $newState New state to check
     * @return array Conflict detection result
     */
    protected function detectConflicts(string $widgetId, array $newState): array
    {
        try {
            $currentState = $this->widgetStates[$widgetId] ?? null;

            if (!$currentState) {
                return ['hasConflict' => false];
            }

            $conflicts = [];

            // Check version conflicts
            if (isset($newState['version']) && isset($currentState['version'])) {
                if ($newState['version'] < $currentState['version']) {
                    $conflicts[] = 'version_behind';
                } elseif ($newState['version'] > $currentState['version'] + 1) {
                    $conflicts[] = 'version_ahead';
                }
            }

            // Check timestamp conflicts
            if (isset($newState['lastUpdated']) && isset($currentState['lastUpdated'])) {
                $newTime = strtotime($newState['lastUpdated']);
                $currentTime = strtotime($currentState['lastUpdated']);

                if ($newTime < $currentTime) {
                    $conflicts[] = 'timestamp_behind';
                }
            }

            // Check value conflicts (if both are dirty)
            if (($currentState['dirty'] ?? false) && ($newState['dirty'] ?? false)) {
                if (json_encode($currentState['value']) !== json_encode($newState['value'])) {
                    $conflicts[] = 'value_conflict';
                }
            }

            return [
                'hasConflict' => !empty($conflicts),
                'conflicts' => $conflicts,
                'currentState' => $currentState,
                'newState' => $newState,
                'resolution' => $this->config['conflictResolution']
            ];
        } catch (\Exception $e) {
            Log::error('Error detecting conflicts', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'detectConflicts',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['hasConflict' => false];
        }
    }

    /**
     * Validate state structure
     * @param array $state State to validate
     * @return bool True if valid
     */
    protected function validateState(array $state): bool
    {
        try {
            // Check required fields
            $requiredFields = ['widgetId'];
            foreach ($requiredFields as $field) {
                if (!isset($state[$field]) || empty($state[$field])) {
                    Log::warning('Missing required state field', [
                        'field' => $field,
                        'state' => array_keys($state)
                    ]);
                    return false;
                }
            }

            // Validate field types
            $typeValidation = [
                'widgetId' => 'string',
                'valid' => 'boolean',
                'dirty' => 'boolean',
                'touched' => 'boolean',
                'focused' => 'boolean',
                'loading' => 'boolean',
                'version' => 'integer'
            ];

            foreach ($typeValidation as $field => $expectedType) {
                if (isset($state[$field])) {
                    $actualType = gettype($state[$field]);
                    if ($actualType !== $expectedType) {
                        Log::warning('Invalid state field type', [
                            'field' => $field,
                            'expectedType' => $expectedType,
                            'actualType' => $actualType
                        ]);
                        return false;
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error validating state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'validateState',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Add entry to state history
     * @param string $widgetId Widget identifier
     * @param string $action Action performed
     * @param array|null $previousState Previous state
     * @param array $newState New state
     * @return void
     */
    protected function addStateHistory(string $widgetId, string $action, ?array $previousState, array $newState): void
    {
        try {
            if (!isset($this->stateHistory[$widgetId])) {
                $this->stateHistory[$widgetId] = [];
            }

            $historyEntry = [
                'action' => $action,
                'timestamp' => now()->toISOString(),
                'previousState' => $previousState,
                'newState' => $newState,
                'userId' => Auth::id(),
                'version' => $newState['version'] ?? null
            ];

            $this->stateHistory[$widgetId][] = $historyEntry;

            // Limit history size
            if (count($this->stateHistory[$widgetId]) > $this->config['maxStateHistory']) {
                $this->stateHistory[$widgetId] = array_slice(
                    $this->stateHistory[$widgetId],
                    -$this->config['maxStateHistory']
                );
            }
        } catch (\Exception $e) {
            Log::error('Error adding state history', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'addStateHistory',
                'widgetId' => $widgetId,
                'action' => $action,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get state history for widget
     * @param string $widgetId Widget identifier
     * @return array State history
     */
    protected function getStateHistory(string $widgetId): array
    {
        try {
            return $this->stateHistory[$widgetId] ?? [];
        } catch (\Exception $e) {
            Log::error('Error getting state history', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'getStateHistory',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Cache widget state
     * @param string $widgetId Widget identifier
     * @param array $state State to cache
     * @return void
     */
    protected function cacheWidgetState(string $widgetId, array $state): void
    {
        try {
            if (!$this->config['enableCaching']) {
                return;
            }

            $cacheKey = "widget_state_{$widgetId}";
            Cache::put($cacheKey, $state, $this->config['cacheDuration']);
        } catch (\Exception $e) {
            Log::error('Error caching widget state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'cacheWidgetState',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get cached widget state
     * @param string $widgetId Widget identifier
     * @return array|null Cached state or null
     */
    protected function getCachedState(string $widgetId): ?array
    {
        try {
            if (!$this->config['enableCaching']) {
                return null;
            }

            $cacheKey = "widget_state_{$widgetId}";
            return Cache::get($cacheKey);
        } catch (\Exception $e) {
            Log::error('Error getting cached state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'getCachedState',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Persist widget state (mock implementation)
     * @param string $widgetId Widget identifier
     * @param array $state State to persist
     * @return void
     */
    protected function persistWidgetState(string $widgetId, array $state): void
    {
        try {
            if (!$this->config['persistState']) {
                return;
            }

            // Mock implementation - store in memory
            // In real implementation, this would save to database
            $this->persistedStates[$widgetId] = $state;

            // If database is enabled, save to database
            if ($this->config['enableDatabase']) {
                $this->saveToDatabase($widgetId, $state);
            }
        } catch (\Exception $e) {
            Log::error('Error persisting widget state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'persistWidgetState',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Load persisted widget state
     * @param string $widgetId Widget identifier
     * @return array|null Persisted state or null
     */
    protected function loadPersistedState(string $widgetId): ?array
    {
        try {
            if (!$this->config['persistState']) {
                return null;
            }

            // Check memory first
            if (isset($this->persistedStates[$widgetId])) {
                return $this->persistedStates[$widgetId];
            }

            // Check database if enabled
            if ($this->config['enableDatabase']) {
                return $this->loadFromDatabase($widgetId);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error loading persisted state', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'loadPersistedState',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Save state to database (mock implementation)
     * @param string $widgetId Widget identifier
     * @param array $state State to save
     * @return void
     */
    protected function saveToDatabase(string $widgetId, array $state): void
    {
        try {
            // Mock implementation
            // In real implementation, this would use Eloquent model or DB facade
            Log::info('State saved to database (mock)', [
                'widgetId' => $widgetId,
                'version' => $state['version'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving state to database', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'saveToDatabase',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Load state from database (mock implementation)
     * @param string $widgetId Widget identifier
     * @return array|null State from database or null
     */
    protected function loadFromDatabase(string $widgetId): ?array
    {
        try {
            // Mock implementation
            // In real implementation, this would query the database
            Log::info('State loaded from database (mock)', [
                'widgetId' => $widgetId
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error loading state from database', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'loadFromDatabase',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Initialize state manager
     * @return void
     */
    protected function initializeStateManager(): void
    {
        try {
            Log::info('StateManager initialized', [
                'config' => $this->config
            ]);
        } catch (\Exception $e) {
            Log::error('Error initializing state manager', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'initializeStateManager',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get all registered widgets
     * @return array Registered widget states
     */
    public function getAllWidgets(): array
    {
        try {
            return $this->widgetStates;
        } catch (\Exception $e) {
            Log::error('Error getting all widgets', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'getAllWidgets',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Clear all widget states
     * @return bool Success status
     */
    public function clearAllStates(): bool
    {
        try {
            $this->widgetStates = [];
            $this->persistedStates = [];
            $this->stateHistory = [];

            // Clear cache
            if ($this->config['enableCaching']) {
                Cache::flush();
            }

            Log::info('All widget states cleared');

            return true;
        } catch (\Exception $e) {
            Log::error('Error clearing all states', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'clearAllStates',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Update configuration
     * @param array $newConfig New configuration settings
     * @return bool Success status
     */
    public function updateConfig(array $newConfig): bool
    {
        try {
            $this->config = array_merge($this->config, $newConfig);

            Log::info('StateManager configuration updated', [
                'config' => $newConfig
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'updateConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get current configuration
     * @return array Current configuration
     */
    public function getConfig(): array
    {
        try {
            return $this->config;
        } catch (\Exception $e) {
            Log::error('Error getting configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'getConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get state statistics
     * @return array State statistics
     */
    public function getStatistics(): array
    {
        try {
            $stats = [
                'totalWidgets' => count($this->widgetStates),
                'activeWidgets' => 0,
                'dirtyWidgets' => 0,
                'synchronizedWidgets' => 0,
                'errorWidgets' => 0,
                'totalStateChanges' => 0,
                'averageVersion' => 0,
                'oldestWidget' => null,
                'newestWidget' => null
            ];

            $totalVersions = 0;
            $oldestTime = null;
            $newestTime = null;

            foreach ($this->widgetStates as $widgetId => $state) {
                // Count active widgets (not loading)
                if (!($state['loading'] ?? false)) {
                    $stats['activeWidgets']++;
                }

                // Count dirty widgets
                if ($state['dirty'] ?? false) {
                    $stats['dirtyWidgets']++;
                }

                // Count synchronized widgets
                if (($state['syncStatus'] ?? 'unknown') === 'synchronized') {
                    $stats['synchronizedWidgets']++;
                }

                // Count error widgets
                if (($state['syncStatus'] ?? 'unknown') === 'error') {
                    $stats['errorWidgets']++;
                }

                // Calculate version statistics
                $version = $state['version'] ?? 1;
                $totalVersions += $version;

                // Track oldest and newest widgets
                $createdAt = $state['createdAt'] ?? $state['lastUpdated'] ?? null;
                if ($createdAt) {
                    $timestamp = strtotime($createdAt);
                    if ($oldestTime === null || $timestamp < $oldestTime) {
                        $oldestTime = $timestamp;
                        $stats['oldestWidget'] = $widgetId;
                    }
                    if ($newestTime === null || $timestamp > $newestTime) {
                        $newestTime = $timestamp;
                        $stats['newestWidget'] = $widgetId;
                    }
                }

                // Count state changes from history
                if (isset($this->stateHistory[$widgetId])) {
                    $stats['totalStateChanges'] += count($this->stateHistory[$widgetId]);
                }
            }

            // Calculate average version
            if ($stats['totalWidgets'] > 0) {
                $stats['averageVersion'] = round($totalVersions / $stats['totalWidgets'], 2);
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Error getting state statistics', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'getStatistics',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Cleanup old states and history
     * @param int $maxAge Maximum age in seconds
     * @return int Number of cleaned up items
     */
    public function cleanup(int $maxAge = 86400): int
    {
        try {
            $cleanedUp = 0;
            $cutoffTime = time() - $maxAge;

            // Clean up old widgets
            foreach ($this->widgetStates as $widgetId => $state) {
                $lastUpdated = $state['lastUpdated'] ?? null;
                if ($lastUpdated && strtotime($lastUpdated) < $cutoffTime) {
                    unset($this->widgetStates[$widgetId]);
                    unset($this->persistedStates[$widgetId]);
                    unset($this->stateHistory[$widgetId]);

                    // Clear from cache
                    if ($this->config['enableCaching']) {
                        Cache::forget("widget_state_{$widgetId}");
                    }

                    $cleanedUp++;
                }
            }

            // Clean up old history entries
            foreach ($this->stateHistory as $widgetId => $history) {
                $originalCount = count($history);
                $this->stateHistory[$widgetId] = array_filter($history, function ($entry) use ($cutoffTime) {
                    return strtotime($entry['timestamp']) >= $cutoffTime;
                });
                $cleanedUp += $originalCount - count($this->stateHistory[$widgetId]);
            }

            if ($cleanedUp > 0) {
                Log::info('State cleanup completed', [
                    'cleanedUpItems' => $cleanedUp,
                    'maxAge' => $maxAge
                ]);
            }

            return $cleanedUp;
        } catch (\Exception $e) {
            Log::error('Error during state cleanup', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'cleanup',
                'maxAge' => $maxAge,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0;
        }
    }

    /**
     * Export widget states for backup
     * @param array $widgetIds Specific widget IDs to export (empty for all)
     * @return array Exported state data
     */
    public function exportStates(array $widgetIds = []): array
    {
        try {
            $export = [
                'timestamp' => now()->toISOString(),
                'version' => '1.0.0',
                'config' => $this->config,
                'widgets' => [],
                'history' => []
            ];

            $targetWidgets = empty($widgetIds) ? array_keys($this->widgetStates) : $widgetIds;

            foreach ($targetWidgets as $widgetId) {
                if (isset($this->widgetStates[$widgetId])) {
                    $export['widgets'][$widgetId] = $this->widgetStates[$widgetId];

                    if (isset($this->stateHistory[$widgetId])) {
                        $export['history'][$widgetId] = $this->stateHistory[$widgetId];
                    }
                }
            }

            Log::info('Widget states exported', [
                'widgetCount' => count($export['widgets']),
                'requestedWidgets' => count($targetWidgets)
            ]);

            return $export;
        } catch (\Exception $e) {
            Log::error('Error exporting widget states', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'exportStates',
                'widgetIds' => $widgetIds,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Import widget states from backup
     * @param array $importData Exported state data
     * @param bool $overwriteExisting Whether to overwrite existing states
     * @return array Import result
     */
    public function importStates(array $importData, bool $overwriteExisting = false): array
    {
        try {
            $result = [
                'success' => true,
                'imported' => 0,
                'skipped' => 0,
                'errors' => []
            ];

            if (!isset($importData['widgets']) || !is_array($importData['widgets'])) {
                $result['success'] = false;
                $result['errors'][] = 'Invalid import data format';
                return $result;
            }

            foreach ($importData['widgets'] as $widgetId => $state) {
                try {
                    // Skip if widget exists and not overwriting
                    if (!$overwriteExisting && isset($this->widgetStates[$widgetId])) {
                        $result['skipped']++;
                        continue;
                    }

                    // Validate state
                    if (!$this->validateState($state)) {
                        $result['errors'][] = "Invalid state for widget {$widgetId}";
                        continue;
                    }

                    // Import state
                    $this->widgetStates[$widgetId] = $state;

                    // Import history if available
                    if (isset($importData['history'][$widgetId])) {
                        $this->stateHistory[$widgetId] = $importData['history'][$widgetId];
                    }

                    // Persist if enabled
                    if ($this->config['persistState']) {
                        $this->persistWidgetState($widgetId, $state);
                    }

                    // Cache if enabled
                    if ($this->config['enableCaching']) {
                        $this->cacheWidgetState($widgetId, $state);
                    }

                    $result['imported']++;
                } catch (\Exception $e) {
                    $result['errors'][] = "Error importing widget {$widgetId}: " . $e->getMessage();
                }
            }

            Log::info('Widget states imported', [
                'imported' => $result['imported'],
                'skipped' => $result['skipped'],
                'errors' => count($result['errors'])
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error importing widget states', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'importStates',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'imported' => 0,
                'skipped' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Get conflict resolution strategies
     * @return array Available conflict resolution strategies
     */
    public function getConflictStrategies(): array
    {
        try {
            return $this->conflictStrategies;
        } catch (\Exception $e) {
            Log::error('Error getting conflict strategies', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'getConflictStrategies',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Validate widget ID format
     * @param string $widgetId Widget ID to validate
     * @return bool True if valid
     */
    protected function isValidWidgetId(string $widgetId): bool
    {
        try {
            return !empty($widgetId) && preg_match('/^[a-zA-Z0-9_-]+$/', $widgetId);
        } catch (\Exception $e) {
            Log::error('Error validating widget ID', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'StateManager.php',
                'method' => 'isValidWidgetId',
                'widgetId' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
