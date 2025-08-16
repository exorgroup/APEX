<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Event manager service for PRO widgets providing event lifecycle management, registration, execution coordination and performance optimization
 * File location: app/Apex/Pro/Widget/PrimeVueBaseWidget/Services/EventManager.php
 */

namespace App\Apex\Pro\Widget\PrimeVueBaseWidget\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EventManager
{
    /**
     * Registered event handlers
     * @var array
     */
    protected array $eventHandlers = [];

    /**
     * Event execution history
     * @var array
     */
    protected array $executionHistory = [];

    /**
     * Performance metrics
     * @var array
     */
    protected array $performanceMetrics = [];

    /**
     * Event batching queue
     * @var array
     */
    protected array $batchQueue = [];

    /**
     * Configuration settings
     * @var array
     */
    protected array $config = [
        'enableBatching' => true,
        'batchSize' => 10,
        'batchTimeout' => 1000,
        'enableMetrics' => true,
        'maxHistorySize' => 100,
        'cacheEvents' => true,
        'cacheDuration' => 3600
    ];

    /**
     * Constructor
     * @param array $config Configuration options
     */
    public function __construct(array $config = [])
    {
        try {
            $this->config = array_merge($this->config, $config);
            $this->initializeManager();
        } catch (\Exception $e) {
            Log::error('Error initializing EventManager', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => '__construct',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Initialize event manager
     * @return void
     */
    protected function initializeManager(): void
    {
        try {
            // Load cached events if enabled
            if ($this->config['cacheEvents']) {
                $this->loadCachedEvents();
            }

            // Initialize performance tracking
            if ($this->config['enableMetrics']) {
                $this->initializeMetrics();
            }

            Log::info('EventManager initialized successfully', [
                'config' => $this->config
            ]);
        } catch (\Exception $e) {
            Log::error('Error initializing event manager', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'initializeManager',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Register event handler
     * @param string $eventName Event name (onClick, onBlur, etc.)
     * @param array $eventConfig Event configuration
     * @return bool Success status
     */
    public function register(string $eventName, array $eventConfig): bool
    {
        try {
            $processedConfig = $this->processEventConfig($eventConfig);

            if (!$processedConfig) {
                Log::warning('Invalid event configuration', [
                    'eventName' => $eventName,
                    'config' => $eventConfig
                ]);
                return false;
            }

            // Store event handler
            $this->eventHandlers[$eventName] = [
                'config' => $processedConfig,
                'registeredAt' => now()->toISOString(),
                'executionCount' => 0,
                'lastExecuted' => null,
                'averageExecutionTime' => 0,
                'errors' => []
            ];

            // Cache if enabled
            if ($this->config['cacheEvents']) {
                $this->cacheEventHandler($eventName, $this->eventHandlers[$eventName]);
            }

            Log::info('Event handler registered', [
                'eventName' => $eventName,
                'type' => $processedConfig['type'],
                'hasDebounce' => isset($processedConfig['debounce']) && $processedConfig['debounce'] > 0
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error registering event handler', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'register',
                'eventName' => $eventName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Execute event handler
     * @param string $eventName Event name to execute
     * @param array $context Event execution context
     * @return array Execution result
     */
    public function execute(string $eventName, array $context = []): array
    {
        try {
            $startTime = microtime(true);

            if (!isset($this->eventHandlers[$eventName])) {
                return [
                    'success' => false,
                    'error' => 'Event handler not found',
                    'eventName' => $eventName
                ];
            }

            $handler = $this->eventHandlers[$eventName];
            $config = $handler['config'];

            // Check if event should be batched
            if ($this->shouldBatchEvent($config)) {
                return $this->addToBatch($eventName, $context);
            }

            // Execute event directly
            $result = $this->executeEventHandler($eventName, $config, $context);

            // Update metrics and history
            $this->updateExecutionMetrics($eventName, $startTime, $result);
            $this->addToHistory($eventName, $context, $result);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error executing event handler', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'execute',
                'eventName' => $eventName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'eventName' => $eventName
            ];
        }
    }

    /**
     * Execute batched events
     * @return array Batch execution results
     */
    public function executeBatch(): array
    {
        try {
            if (empty($this->batchQueue)) {
                return ['success' => true, 'executed' => 0];
            }

            $results = [];
            $executedCount = 0;

            foreach ($this->batchQueue as $batchItem) {
                $result = $this->executeEventHandler(
                    $batchItem['eventName'],
                    $batchItem['config'],
                    $batchItem['context']
                );

                $results[] = $result;
                $executedCount++;

                // Update metrics
                $this->updateExecutionMetrics($batchItem['eventName'], $batchItem['queuedAt'], $result);
            }

            // Clear batch queue
            $this->batchQueue = [];

            Log::info('Batch events executed', [
                'executedCount' => $executedCount,
                'successCount' => count(array_filter($results, fn($r) => $r['success']))
            ]);

            return [
                'success' => true,
                'executed' => $executedCount,
                'results' => $results
            ];
        } catch (\Exception $e) {
            Log::error('Error executing batch events', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'executeBatch',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'executed' => 0
            ];
        }
    }

    /**
     * Get event handler information
     * @param string|null $eventName Specific event name (null for all)
     * @return array Event handler information
     */
    public function getHandlerInfo(?string $eventName = null): array
    {
        try {
            if ($eventName) {
                return $this->eventHandlers[$eventName] ?? [];
            }

            return $this->eventHandlers;
        } catch (\Exception $e) {
            Log::error('Error getting handler info', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'getHandlerInfo',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get performance metrics
     * @return array Performance metrics
     */
    public function getMetrics(): array
    {
        try {
            return [
                'totalEvents' => count($this->eventHandlers),
                'totalExecutions' => array_sum(array_column($this->eventHandlers, 'executionCount')),
                'averageExecutionTime' => $this->calculateAverageExecutionTime(),
                'errorRate' => $this->calculateErrorRate(),
                'batchQueueSize' => count($this->batchQueue),
                'historySize' => count($this->executionHistory),
                'performanceMetrics' => $this->performanceMetrics
            ];
        } catch (\Exception $e) {
            Log::error('Error getting metrics', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'getMetrics',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Clear event handlers
     * @param string|null $eventName Specific event to clear (null for all)
     * @return bool Success status
     */
    public function clear(?string $eventName = null): bool
    {
        try {
            if ($eventName) {
                if (isset($this->eventHandlers[$eventName])) {
                    unset($this->eventHandlers[$eventName]);

                    // Remove from cache
                    if ($this->config['cacheEvents']) {
                        Cache::forget("event_handler_{$eventName}");
                    }

                    Log::info('Event handler cleared', ['eventName' => $eventName]);
                    return true;
                }
                return false;
            }

            // Clear all handlers
            $this->eventHandlers = [];
            $this->batchQueue = [];
            $this->executionHistory = [];

            // Clear cache
            if ($this->config['cacheEvents']) {
                Cache::forget('event_handlers_all');
            }

            Log::info('All event handlers cleared');
            return true;
        } catch (\Exception $e) {
            Log::error('Error clearing event handlers', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'clear',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process event configuration
     * @param array $eventConfig Raw event configuration
     * @return array|null Processed configuration or null if invalid
     */
    protected function processEventConfig(array $eventConfig): ?array
    {
        try {
            // Validate required fields
            if (!isset($eventConfig['type'])) {
                return null;
            }

            $processed = [
                'type' => $eventConfig['type'],
                'handler' => $eventConfig['handler'] ?? null,
                'server' => $eventConfig['server'] ?? null,
                'debounce' => max(0, $eventConfig['debounce'] ?? 0),
                'throttle' => max(0, $eventConfig['throttle'] ?? 0),
                'async' => $eventConfig['async'] ?? true,
                'params' => $eventConfig['params'] ?? [],
                'errorHandling' => $eventConfig['errorHandling'] ?? 'default',
                'priority' => $eventConfig['priority'] ?? 'normal',
                'timeout' => $eventConfig['timeout'] ?? 30000,
                'retries' => $eventConfig['retries'] ?? 0
            ];

            // Process multiple handlers
            if ($eventConfig['type'] === 'multiple' && isset($eventConfig['handlers'])) {
                $processed['handlers'] = [];
                foreach ($eventConfig['handlers'] as $handler) {
                    $processedHandler = $this->processEventConfig($handler);
                    if ($processedHandler) {
                        $processed['handlers'][] = $processedHandler;
                    }
                }
                $processed['execution'] = $eventConfig['execution'] ?? 'parallel';
            }

            return $processed;
        } catch (\Exception $e) {
            Log::error('Error processing event configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'processEventConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Execute single event handler
     * @param string $eventName Event name
     * @param array $config Event configuration
     * @param array $context Execution context
     * @return array Execution result
     */
    protected function executeEventHandler(string $eventName, array $config, array $context): array
    {
        try {
            $startTime = microtime(true);
            $result = [
                'success' => true,
                'eventName' => $eventName,
                'executionTime' => 0,
                'results' => []
            ];

            switch ($config['type']) {
                case 'single':
                    $result['results'][] = $this->executeSingleHandler($config, $context);
                    break;

                case 'multiple':
                    $result['results'] = $this->executeMultipleHandlers($config, $context);
                    break;

                case 'advanced':
                    $result['results'][] = $this->executeAdvancedHandler($config, $context);
                    break;

                default:
                    throw new \Exception("Unknown event type: {$config['type']}");
            }

            // Calculate execution time
            $result['executionTime'] = round((microtime(true) - $startTime) * 1000, 2);

            // Check for any failures
            $hasFailures = false;
            foreach ($result['results'] as $handlerResult) {
                if (!$handlerResult['success']) {
                    $hasFailures = true;
                    break;
                }
            }

            $result['success'] = !$hasFailures;

            return $result;
        } catch (\Exception $e) {
            Log::error('Error executing event handler', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'executeEventHandler',
                'eventName' => $eventName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'eventName' => $eventName,
                'error' => $e->getMessage(),
                'executionTime' => 0,
                'results' => []
            ];
        }
    }

    /**
     * Execute single handler
     * @param array $config Handler configuration
     * @param array $context Execution context
     * @return array Handler result
     */
    protected function executeSingleHandler(array $config, array $context): array
    {
        try {
            $results = [];

            // Execute client handler
            if ($config['handler']) {
                $results['client'] = [
                    'success' => true,
                    'handler' => $config['handler'],
                    'type' => 'client',
                    'message' => 'Client handler registered for execution'
                ];
            }

            // Execute server handler
            if ($config['server']) {
                $results['server'] = $this->executeServerHandler($config, $context);
            }

            return [
                'success' => true,
                'type' => 'single',
                'results' => $results
            ];
        } catch (\Exception $e) {
            Log::error('Error executing single handler', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'executeSingleHandler',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'type' => 'single',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Execute multiple handlers
     * @param array $config Handler configuration
     * @param array $context Execution context
     * @return array Handler results
     */
    protected function executeMultipleHandlers(array $config, array $context): array
    {
        try {
            $results = [];
            $execution = $config['execution'] ?? 'parallel';

            if ($execution === 'sequential') {
                // Execute handlers sequentially
                foreach ($config['handlers'] as $index => $handler) {
                    $result = $this->executeEventHandler("handler_{$index}", $handler, $context);
                    $results[] = $result;

                    // Stop on first failure if configured
                    if (!$result['success'] && ($handler['stopOnFailure'] ?? false)) {
                        break;
                    }
                }
            } else {
                // Execute handlers in parallel (simulated)
                foreach ($config['handlers'] as $index => $handler) {
                    $result = $this->executeEventHandler("handler_{$index}", $handler, $context);
                    $results[] = $result;
                }
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('Error executing multiple handlers', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'executeMultipleHandlers',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [[
                'success' => false,
                'type' => 'multiple',
                'error' => $e->getMessage()
            ]];
        }
    }

    /**
     * Execute advanced handler
     * @param array $config Handler configuration
     * @param array $context Execution context
     * @return array Handler result
     */
    protected function executeAdvancedHandler(array $config, array $context): array
    {
        try {
            $results = [];

            // Execute client handler first for immediate feedback
            if ($config['handler']) {
                $results['client'] = [
                    'success' => true,
                    'handler' => $config['handler'],
                    'type' => 'client',
                    'immediate' => true,
                    'message' => 'Client handler executed for immediate feedback'
                ];
            }

            // Execute server handler asynchronously
            if ($config['server']) {
                $results['server'] = $this->executeServerHandler($config, $context);
            }

            return [
                'success' => true,
                'type' => 'advanced',
                'results' => $results
            ];
        } catch (\Exception $e) {
            Log::error('Error executing advanced handler', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'executeAdvancedHandler',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'type' => 'advanced',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Execute server handler
     * @param array $config Handler configuration
     * @param array $context Execution context
     * @return array Server execution result
     */
    protected function executeServerHandler(array $config, array $context): array
    {
        try {
            // Parse server endpoint
            $serverEndpoint = $config['server'];
            if (!str_starts_with($serverEndpoint, '@')) {
                throw new \Exception('Server endpoint must start with @');
            }

            // Extract controller and method
            $endpoint = substr($serverEndpoint, 1); // Remove @
            $parts = explode('@', $endpoint);

            if (count($parts) !== 2) {
                throw new \Exception('Invalid server endpoint format. Expected @Controller@method');
            }

            [$controller, $method] = $parts;

            // Prepare server execution data
            $serverData = [
                'controller' => $controller,
                'method' => $method,
                'params' => $config['params'] ?? [],
                'context' => $context,
                'timeout' => $config['timeout'] ?? 30000,
                'retries' => $config['retries'] ?? 0
            ];

            // Note: Actual server execution would happen on client-side
            // This just validates and prepares the configuration
            return [
                'success' => true,
                'type' => 'server',
                'endpoint' => $serverEndpoint,
                'controller' => $controller,
                'method' => $method,
                'data' => $serverData,
                'message' => 'Server handler prepared for execution'
            ];
        } catch (\Exception $e) {
            Log::error('Error executing server handler', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'executeServerHandler',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'type' => 'server',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if event should be batched
     * @param array $config Event configuration
     * @return bool Whether to batch the event
     */
    protected function shouldBatchEvent(array $config): bool
    {
        try {
            if (!$this->config['enableBatching']) {
                return false;
            }

            // Don't batch high-priority events
            if (($config['priority'] ?? 'normal') === 'high') {
                return false;
            }

            // Don't batch events with immediate handlers
            if ($config['type'] === 'advanced' && $config['handler']) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error checking if event should be batched', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'shouldBatchEvent',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Add event to batch queue
     * @param string $eventName Event name
     * @param array $context Event context
     * @return array Batch result
     */
    protected function addToBatch(string $eventName, array $context): array
    {
        try {
            $this->batchQueue[] = [
                'eventName' => $eventName,
                'config' => $this->eventHandlers[$eventName]['config'],
                'context' => $context,
                'queuedAt' => microtime(true)
            ];

            // Execute batch if queue is full
            if (count($this->batchQueue) >= $this->config['batchSize']) {
                return $this->executeBatch();
            }

            return [
                'success' => true,
                'batched' => true,
                'queueSize' => count($this->batchQueue),
                'eventName' => $eventName
            ];
        } catch (\Exception $e) {
            Log::error('Error adding event to batch', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'addToBatch',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'batched' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update execution metrics
     * @param string $eventName Event name
     * @param float $startTime Start time
     * @param array $result Execution result
     * @return void
     */
    protected function updateExecutionMetrics(string $eventName, float $startTime, array $result): void
    {
        try {
            if (!$this->config['enableMetrics']) {
                return;
            }

            $executionTime = microtime(true) - $startTime;
            $handler = &$this->eventHandlers[$eventName];

            $handler['executionCount']++;
            $handler['lastExecuted'] = now()->toISOString();

            // Calculate running average
            $totalTime = $handler['averageExecutionTime'] * ($handler['executionCount'] - 1) + $executionTime;
            $handler['averageExecutionTime'] = $totalTime / $handler['executionCount'];

            // Track errors
            if (!$result['success']) {
                $handler['errors'][] = [
                    'timestamp' => now()->toISOString(),
                    'error' => $result['error'] ?? 'Unknown error',
                    'context' => $result['context'] ?? []
                ];

                // Keep only last 10 errors
                if (count($handler['errors']) > 10) {
                    $handler['errors'] = array_slice($handler['errors'], -10);
                }
            }

            // Update global metrics
            if (!isset($this->performanceMetrics[$eventName])) {
                $this->performanceMetrics[$eventName] = [
                    'totalExecutions' => 0,
                    'totalTime' => 0,
                    'errors' => 0,
                    'averageTime' => 0
                ];
            }

            $metrics = &$this->performanceMetrics[$eventName];
            $metrics['totalExecutions']++;
            $metrics['totalTime'] += $executionTime;
            $metrics['averageTime'] = $metrics['totalTime'] / $metrics['totalExecutions'];

            if (!$result['success']) {
                $metrics['errors']++;
            }
        } catch (\Exception $e) {
            Log::error('Error updating execution metrics', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'updateExecutionMetrics',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Add execution to history
     * @param string $eventName Event name
     * @param array $context Execution context
     * @param array $result Execution result
     * @return void
     */
    protected function addToHistory(string $eventName, array $context, array $result): void
    {
        try {
            $this->executionHistory[] = [
                'eventName' => $eventName,
                'timestamp' => now()->toISOString(),
                'context' => $context,
                'result' => $result,
                'success' => $result['success'],
                'executionTime' => $result['executionTime'] ?? 0
            ];

            // Limit history size
            if (count($this->executionHistory) > $this->config['maxHistorySize']) {
                $this->executionHistory = array_slice($this->executionHistory, -$this->config['maxHistorySize']);
            }
        } catch (\Exception $e) {
            Log::error('Error adding to execution history', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'addToHistory',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Calculate average execution time
     * @return float Average execution time in milliseconds
     */
    protected function calculateAverageExecutionTime(): float
    {
        try {
            if (empty($this->performanceMetrics)) {
                return 0.0;
            }

            $totalTime = array_sum(array_column($this->performanceMetrics, 'totalTime'));
            $totalExecutions = array_sum(array_column($this->performanceMetrics, 'totalExecutions'));

            return $totalExecutions > 0 ? ($totalTime / $totalExecutions) * 1000 : 0.0;
        } catch (\Exception $e) {
            Log::error('Error calculating average execution time', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'calculateAverageExecutionTime',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0.0;
        }
    }

    /**
     * Calculate error rate
     * @return float Error rate as percentage
     */
    protected function calculateErrorRate(): float
    {
        try {
            if (empty($this->performanceMetrics)) {
                return 0.0;
            }

            $totalErrors = array_sum(array_column($this->performanceMetrics, 'errors'));
            $totalExecutions = array_sum(array_column($this->performanceMetrics, 'totalExecutions'));

            return $totalExecutions > 0 ? ($totalErrors / $totalExecutions) * 100 : 0.0;
        } catch (\Exception $e) {
            Log::error('Error calculating error rate', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'calculateErrorRate',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0.0;
        }
    }

    /**
     * Load cached events
     * @return void
     */
    protected function loadCachedEvents(): void
    {
        try {
            $cachedHandlers = Cache::get('event_handlers_all', []);
            if (!empty($cachedHandlers)) {
                $this->eventHandlers = $cachedHandlers;
                Log::info('Cached event handlers loaded', [
                    'count' => count($cachedHandlers)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error loading cached events', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'loadCachedEvents',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Cache event handler
     * @param string $eventName Event name
     * @param array $handler Handler data
     * @return void
     */
    protected function cacheEventHandler(string $eventName, array $handler): void
    {
        try {
            Cache::put("event_handler_{$eventName}", $handler, $this->config['cacheDuration']);
            Cache::put('event_handlers_all', $this->eventHandlers, $this->config['cacheDuration']);
        } catch (\Exception $e) {
            Log::error('Error caching event handler', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'cacheEventHandler',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Initialize metrics tracking
     * @return void
     */
    protected function initializeMetrics(): void
    {
        try {
            $this->performanceMetrics = [];
            Log::info('Performance metrics tracking initialized');
        } catch (\Exception $e) {
            Log::error('Error initializing metrics', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'initializeMetrics',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get execution history
     * @param int $limit Number of records to return
     * @return array Execution history
     */
    public function getExecutionHistory(int $limit = 50): array
    {
        try {
            return array_slice($this->executionHistory, -$limit);
        } catch (\Exception $e) {
            Log::error('Error getting execution history', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'EventManager.php',
                'method' => 'getExecutionHistory',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
