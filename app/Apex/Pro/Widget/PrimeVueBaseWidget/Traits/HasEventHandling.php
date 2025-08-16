<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Event handling trait for PRO widgets providing Vue method and Laravel controller event binding with debouncing and async support
 * File location: app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits/HasEventHandling.php
 */

namespace App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits;

use App\Apex\Pro\Widget\PrimeVueBaseWidget\Services\EventManager;
use Illuminate\Support\Facades\Log;

trait HasEventHandling
{
    /**
     * Event configuration storage
     * @var array
     */
    protected array $eventConfig = [];

    /**
     * Event manager instance
     * @var EventManager|null
     */
    protected ?EventManager $eventManager = null;

    /**
     * Initialize event handling system
     * @param array $config Widget configuration containing event definitions
     * @return void
     */
    protected function initializeEventHandling(array $config): void
    {
        try {
            if (isset($config['events'])) {
                $this->eventConfig = $config['events'];
                $this->eventManager = new EventManager();
                $this->registerEvents($this->eventConfig);
            }
        } catch (\Exception $e) {
            Log::error('Error initializing event handling', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'initializeEventHandling',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get event schema for widget validation
     * @return array JSON schema definition for events
     */
    protected function getEventSchema(): array
    {
        try {
            return [
                'type' => 'object',
                'description' => 'Event handlers configuration',
                'properties' => [
                    'onClick' => [
                        'oneOf' => [
                            ['type' => 'string', 'description' => 'Vue method name or @Controller@method'],
                            ['type' => 'array', 'description' => 'Multiple handlers'],
                            ['type' => 'object', 'description' => 'Advanced configuration']
                        ]
                    ],
                    'onBlur' => [
                        'oneOf' => [
                            ['type' => 'string'],
                            ['type' => 'array'],
                            ['type' => 'object']
                        ]
                    ],
                    'onFocus' => [
                        'oneOf' => [
                            ['type' => 'string'],
                            ['type' => 'array'],
                            ['type' => 'object']
                        ]
                    ],
                    'onInput' => [
                        'oneOf' => [
                            ['type' => 'string'],
                            ['type' => 'array'],
                            ['type' => 'object']
                        ]
                    ],
                    'onChange' => [
                        'oneOf' => [
                            ['type' => 'string'],
                            ['type' => 'array'],
                            ['type' => 'object']
                        ]
                    ],
                    'onKeydown' => [
                        'oneOf' => [
                            ['type' => 'string'],
                            ['type' => 'array'],
                            ['type' => 'object']
                        ]
                    ],
                    'onKeyup' => [
                        'oneOf' => [
                            ['type' => 'string'],
                            ['type' => 'array'],
                            ['type' => 'object']
                        ]
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting event schema', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'getEventSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Process event configuration and normalize handlers
     * @param array $events Raw event configuration from JSON
     * @return array Processed and normalized event configuration
     */
    protected function processEvents(array $events): array
    {
        try {
            $processedEvents = [];

            foreach ($events as $eventName => $eventConfig) {
                $processedEvents[$eventName] = $this->normalizeEventHandler($eventConfig);
            }

            return $processedEvents;
        } catch (\Exception $e) {
            Log::error('Error processing events', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'processEvents',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $events;
        }
    }

    /**
     * Normalize event handler configuration to standard format
     * @param mixed $eventConfig Single event configuration (string, array, or object)
     * @return array Normalized event handler configuration
     */
    protected function normalizeEventHandler($eventConfig): array
    {
        try {
            // Handle string format: "handleClick" or "@Controller@method"
            if (is_string($eventConfig)) {
                return [
                    'type' => 'single',
                    'handler' => $eventConfig,
                    'debounce' => $this->getDefaultDebounce($eventConfig),
                    'async' => $this->isServerHandler($eventConfig)
                ];
            }

            // Handle array format: ["showHelp", "@log"]
            if (is_array($eventConfig) && !$this->isAssociativeArray($eventConfig)) {
                return [
                    'type' => 'multiple',
                    'handlers' => array_map([$this, 'normalizeEventHandler'], $eventConfig),
                    'execution' => 'parallel'
                ];
            }

            // Handle object format: {"handler": "validateInput", "server": "@Controller@method", "debounce": 300}
            if (is_array($eventConfig) && $this->isAssociativeArray($eventConfig)) {
                $normalized = [
                    'type' => 'advanced',
                    'handler' => $eventConfig['handler'] ?? null,
                    'server' => $eventConfig['server'] ?? null,
                    'debounce' => $eventConfig['debounce'] ?? $this->getDefaultDebounce($eventConfig['handler'] ?? ''),
                    'throttle' => $eventConfig['throttle'] ?? null,
                    'async' => $eventConfig['async'] ?? true,
                    'params' => $eventConfig['params'] ?? [],
                    'errorHandling' => $eventConfig['errorHandling'] ?? 'default'
                ];

                return $normalized;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error normalizing event handler', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'normalizeEventHandler',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Register events with the event manager
     * @param array $events Processed event configuration
     * @return void
     */
    protected function registerEvents(array $events): void
    {
        try {
            if ($this->eventManager) {
                foreach ($events as $eventName => $eventConfig) {
                    $this->eventManager->register($eventName, $eventConfig);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error registering events', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'registerEvents',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Check if handler is a server-side Laravel controller method
     * @param string $handler Handler string to check
     * @return bool True if handler starts with @ indicating server method
     */
    protected function isServerHandler(string $handler): bool
    {
        try {
            return str_starts_with($handler, '@');
        } catch (\Exception $e) {
            Log::error('Error checking server handler', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'isServerHandler',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get default debounce time based on event type
     * @param string $handler Handler name to determine debounce timing
     * @return int Default debounce time in milliseconds
     */
    protected function getDefaultDebounce(string $handler): int
    {
        try {
            // Input events need debouncing
            if (str_contains($handler, 'input') || str_contains($handler, 'keydown') || str_contains($handler, 'keyup')) {
                return 300;
            }

            // Click and focus events are immediate
            return 0;
        } catch (\Exception $e) {
            Log::error('Error getting default debounce', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'getDefaultDebounce',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0;
        }
    }

    /**
     * Check if array is associative (has string keys)
     * @param array $array Array to check
     * @return bool True if array is associative
     */
    protected function isAssociativeArray(array $array): bool
    {
        try {
            return array_keys($array) !== range(0, count($array) - 1);
        } catch (\Exception $e) {
            Log::error('Error checking associative array', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'isAssociativeArray',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Check if widget has event handling capability
     * @return bool True if event handling is available
     */
    protected function hasEventHandling(): bool
    {
        try {
            return !empty($this->eventConfig) && $this->eventManager !== null;
        } catch (\Exception $e) {
            Log::error('Error checking event handling capability', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'hasEventHandling',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get current event configuration
     * @return array Current event configuration
     */
    public function getEventConfig(): array
    {
        try {
            return $this->eventConfig;
        } catch (\Exception $e) {
            Log::error('Error getting event configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasEventHandling.php',
                'method' => 'getEventConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
