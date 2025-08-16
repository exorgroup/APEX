<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Error handling trait for PRO widgets providing configurable error display strategies (inline, toast, dialog) with field targeting and auto-dismiss functionality
 * File location: app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits/HasErrorHandling.php
 */

namespace App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits;

use Illuminate\Support\Facades\Log;

trait HasErrorHandling
{
    /**
     * Error configuration storage
     * @var array
     */
    protected array $errorConfig = [];

    /**
     * Current error state
     * @var array
     */
    protected array $currentErrors = [];

    /**
     * Error display settings
     * @var array
     */
    protected array $errorSettings = [
        'displayType' => 'inline',
        'position' => 'bottom',
        'timeout' => 5000,
        'autoClose' => true,
        'showIcon' => true,
        'allowDismiss' => true,
        'stackErrors' => false
    ];

    /**
     * Supported error display types
     * @var array
     */
    protected array $supportedDisplayTypes = ['inline', 'toast', 'dialog'];

    /**
     * Error severity levels
     * @var array
     */
    protected array $errorSeverities = ['info', 'success', 'warn', 'error'];

    /**
     * Initialize error handling system
     * @param array $config Widget configuration containing error definitions
     * @return void
     */
    protected function initializeErrorHandling(array $config): void
    {
        try {
            if (isset($config['errorConfig'])) {
                $this->errorConfig = $config['errorConfig'];
                $this->errorSettings = array_merge($this->errorSettings, $this->errorConfig);
                $this->validateErrorSettings();
            }
        } catch (\Exception $e) {
            Log::error('Error initializing error handling', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'initializeErrorHandling',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get error schema for widget validation
     * @return array JSON schema definition for error handling
     */
    protected function getErrorSchema(): array
    {
        try {
            return [
                'type' => 'object',
                'description' => 'Error handling configuration',
                'properties' => [
                    'displayType' => [
                        'type' => 'string',
                        'description' => 'Error display method',
                        'enum' => $this->supportedDisplayTypes,
                        'default' => 'inline'
                    ],
                    'position' => [
                        'type' => 'string',
                        'description' => 'Error position for inline display',
                        'enum' => ['top', 'bottom', 'left', 'right'],
                        'default' => 'bottom'
                    ],
                    'timeout' => [
                        'type' => 'integer',
                        'description' => 'Auto-dismiss timeout in milliseconds',
                        'default' => 5000,
                        'minimum' => 1000
                    ],
                    'autoClose' => [
                        'type' => 'boolean',
                        'description' => 'Automatically close errors after timeout',
                        'default' => true
                    ],
                    'showIcon' => [
                        'type' => 'boolean',
                        'description' => 'Show severity icon with error',
                        'default' => true
                    ],
                    'allowDismiss' => [
                        'type' => 'boolean',
                        'description' => 'Allow manual error dismissal',
                        'default' => true
                    ],
                    'stackErrors' => [
                        'type' => 'boolean',
                        'description' => 'Stack multiple errors or replace',
                        'default' => false
                    ],
                    'customStyling' => [
                        'type' => 'object',
                        'description' => 'Custom CSS styling for errors',
                        'properties' => [
                            'className' => ['type' => 'string'],
                            'style' => ['type' => 'object']
                        ]
                    ],
                    'fieldMapping' => [
                        'type' => 'object',
                        'description' => 'Map error codes to specific fields',
                        'patternProperties' => [
                            '^.*$' => ['type' => 'string']
                        ]
                    ],
                    'localization' => [
                        'type' => 'object',
                        'description' => 'Localized error messages',
                        'properties' => [
                            'defaultLocale' => ['type' => 'string'],
                            'messages' => [
                                'type' => 'object',
                                'patternProperties' => [
                                    '^.*$' => [
                                        'type' => 'object',
                                        'patternProperties' => [
                                            '^.*$' => ['type' => 'string']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting error schema', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'getErrorSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Process error configuration and validate settings
     * @param array $errorConfig Raw error configuration from JSON
     * @return array Processed error configuration
     */
    protected function processErrorConfig(array $errorConfig): array
    {
        try {
            $processedConfig = [
                'displayType' => $this->validateDisplayType($errorConfig['displayType'] ?? 'inline'),
                'position' => $this->validatePosition($errorConfig['position'] ?? 'bottom'),
                'timeout' => max(1000, $errorConfig['timeout'] ?? 5000),
                'autoClose' => $errorConfig['autoClose'] ?? true,
                'showIcon' => $errorConfig['showIcon'] ?? true,
                'allowDismiss' => $errorConfig['allowDismiss'] ?? true,
                'stackErrors' => $errorConfig['stackErrors'] ?? false,
                'customStyling' => $errorConfig['customStyling'] ?? [],
                'fieldMapping' => $errorConfig['fieldMapping'] ?? [],
                'localization' => $errorConfig['localization'] ?? []
            ];

            return $processedConfig;
        } catch (\Exception $e) {
            Log::error('Error processing error configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'processErrorConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $errorConfig;
        }
    }

    /**
     * Add error to current error state
     * @param string $message Error message
     * @param string $severity Error severity (info, success, warn, error)
     * @param string|null $field Target field for error
     * @param string|null $code Error code for identification
     * @param array $context Additional error context
     * @return string Error ID for tracking
     */
    public function addError(
        string $message,
        string $severity = 'error',
        ?string $field = null,
        ?string $code = null,
        array $context = []
    ): string {
        try {
            $errorId = uniqid('error_');
            $severity = $this->validateSeverity($severity);

            $error = [
                'id' => $errorId,
                'message' => $this->localizeMessage($message, $code),
                'severity' => $severity,
                'field' => $field,
                'code' => $code,
                'context' => $context,
                'displayType' => $this->determineDisplayType($severity, $field),
                'timestamp' => now()->toISOString(),
                'dismissed' => false,
                'autoClose' => $this->shouldAutoClose($severity)
            ];

            // Handle stacking vs replacement
            if (!$this->errorSettings['stackErrors']) {
                $this->clearErrors($field);
            }

            $this->currentErrors[$errorId] = $error;

            // Log error for debugging
            Log::info('Error added to widget', [
                'widgetId' => $this->getCurrentWidgetId(),
                'errorId' => $errorId,
                'message' => $message,
                'severity' => $severity,
                'field' => $field,
                'code' => $code
            ]);

            return $errorId;
        } catch (\Exception $e) {
            Log::error('Error adding error', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'addError',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return '';
        }
    }

    /**
     * Remove error by ID
     * @param string $errorId Error ID to remove
     * @return bool Success status
     */
    public function removeError(string $errorId): bool
    {
        try {
            if (isset($this->currentErrors[$errorId])) {
                unset($this->currentErrors[$errorId]);

                Log::info('Error removed from widget', [
                    'widgetId' => $this->getCurrentWidgetId(),
                    'errorId' => $errorId
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error removing error', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'removeError',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Clear all errors or errors for specific field
     * @param string|null $field Field to clear errors for (null for all)
     * @return int Number of errors cleared
     */
    public function clearErrors(?string $field = null): int
    {
        try {
            $clearedCount = 0;

            if ($field === null) {
                // Clear all errors
                $clearedCount = count($this->currentErrors);
                $this->currentErrors = [];
            } else {
                // Clear errors for specific field
                foreach ($this->currentErrors as $errorId => $error) {
                    if ($error['field'] === $field) {
                        unset($this->currentErrors[$errorId]);
                        $clearedCount++;
                    }
                }
            }

            if ($clearedCount > 0) {
                Log::info('Errors cleared from widget', [
                    'widgetId' => $this->getCurrentWidgetId(),
                    'field' => $field,
                    'clearedCount' => $clearedCount
                ]);
            }

            return $clearedCount;
        } catch (\Exception $e) {
            Log::error('Error clearing errors', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'clearErrors',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0;
        }
    }

    /**
     * Get all current errors
     * @param string|null $field Get errors for specific field (null for all)
     * @param string|null $severity Filter by severity
     * @return array Current errors
     */
    public function getErrors(?string $field = null, ?string $severity = null): array
    {
        try {
            $errors = $this->currentErrors;

            // Filter by field
            if ($field !== null) {
                $errors = array_filter($errors, fn($error) => $error['field'] === $field);
            }

            // Filter by severity
            if ($severity !== null) {
                $errors = array_filter($errors, fn($error) => $error['severity'] === $severity);
            }

            return array_values($errors);
        } catch (\Exception $e) {
            Log::error('Error getting errors', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'getErrors',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Check if widget has errors
     * @param string|null $field Check specific field (null for any)
     * @param string|null $severity Check specific severity (null for any)
     * @return bool True if errors exist
     */
    public function hasErrors(?string $field = null, ?string $severity = null): bool
    {
        try {
            return count($this->getErrors($field, $severity)) > 0;
        } catch (\Exception $e) {
            Log::error('Error checking for errors', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'hasErrors',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get errors formatted for client-side display
     * @return array Client-formatted errors
     */
    public function getClientErrors(): array
    {
        try {
            $clientErrors = [];

            foreach ($this->currentErrors as $error) {
                if (!$error['dismissed']) {
                    $clientErrors[] = [
                        'id' => $error['id'],
                        'message' => $error['message'],
                        'severity' => $error['severity'],
                        'field' => $error['field'],
                        'displayType' => $error['displayType'],
                        'autoClose' => $error['autoClose'],
                        'timeout' => $this->errorSettings['timeout'],
                        'showIcon' => $this->errorSettings['showIcon'],
                        'allowDismiss' => $this->errorSettings['allowDismiss'],
                        'position' => $this->errorSettings['position'],
                        'customStyling' => $this->errorSettings['customStyling']
                    ];
                }
            }

            return $clientErrors;
        } catch (\Exception $e) {
            Log::error('Error getting client errors', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'getClientErrors',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Process server error response
     * @param array $serverResponse Server response containing error information
     * @return string|null Error ID if error was added
     */
    public function processServerError(array $serverResponse): ?string
    {
        try {
            if (!isset($serverResponse['success']) || $serverResponse['success']) {
                return null; // No error to process
            }

            $message = $serverResponse['message'] ?? 'An error occurred';
            $field = $serverResponse['field'] ?? null;
            $code = $serverResponse['code'] ?? 'SERVER_ERROR';
            $severity = $this->mapServerSeverity($serverResponse['severity'] ?? 'error');

            return $this->addError($message, $severity, $field, $code, [
                'source' => 'server',
                'response' => $serverResponse
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing server error', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'processServerError',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Validate error display type
     * @param string $displayType Display type to validate
     * @return string Valid display type
     */
    protected function validateDisplayType(string $displayType): string
    {
        try {
            if (in_array($displayType, $this->supportedDisplayTypes)) {
                return $displayType;
            }

            Log::warning('Invalid error display type, defaulting to inline', [
                'invalidType' => $displayType,
                'supportedTypes' => $this->supportedDisplayTypes
            ]);

            return 'inline';
        } catch (\Exception $e) {
            Log::error('Error validating display type', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'validateDisplayType',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'inline';
        }
    }

    /**
     * Validate error position
     * @param string $position Position to validate
     * @return string Valid position
     */
    protected function validatePosition(string $position): string
    {
        try {
            $validPositions = ['top', 'bottom', 'left', 'right'];

            if (in_array($position, $validPositions)) {
                return $position;
            }

            return 'bottom';
        } catch (\Exception $e) {
            Log::error('Error validating position', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'validatePosition',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'bottom';
        }
    }

    /**
     * Validate error severity
     * @param string $severity Severity to validate
     * @return string Valid severity
     */
    protected function validateSeverity(string $severity): string
    {
        try {
            if (in_array($severity, $this->errorSeverities)) {
                return $severity;
            }

            return 'error';
        } catch (\Exception $e) {
            Log::error('Error validating severity', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'validateSeverity',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'error';
        }
    }

    /**
     * Determine display type based on severity and field
     * @param string $severity Error severity
     * @param string|null $field Target field
     * @return string Display type to use
     */
    protected function determineDisplayType(string $severity, ?string $field): string
    {
        try {
            // Field-specific errors are typically inline
            if ($field !== null) {
                return 'inline';
            }

            // System errors can be toast or dialog based on severity
            return match ($severity) {
                'error' => $this->errorSettings['displayType'],
                'warn' => 'toast',
                'info', 'success' => 'toast',
                default => $this->errorSettings['displayType']
            };
        } catch (\Exception $e) {
            Log::error('Error determining display type', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'determineDisplayType',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorSettings['displayType'];
        }
    }

    /**
     * Determine if error should auto-close
     * @param string $severity Error severity
     * @return bool Whether error should auto-close
     */
    protected function shouldAutoClose(string $severity): bool
    {
        try {
            // Critical errors should not auto-close
            if ($severity === 'error') {
                return false;
            }

            return $this->errorSettings['autoClose'];
        } catch (\Exception $e) {
            Log::error('Error determining auto-close', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'shouldAutoClose',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorSettings['autoClose'];
        }
    }

    /**
     * Localize error message
     * @param string $message Original message
     * @param string|null $code Error code for lookup
     * @return string Localized message
     */
    protected function localizeMessage(string $message, ?string $code): string
    {
        try {
            if (!$code || empty($this->errorSettings['localization'])) {
                return $message;
            }

            $locale = $this->errorSettings['localization']['defaultLocale'] ?? 'en';
            $messages = $this->errorSettings['localization']['messages'] ?? [];

            if (isset($messages[$locale][$code])) {
                return $messages[$locale][$code];
            }

            return $message;
        } catch (\Exception $e) {
            Log::error('Error localizing message', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'localizeMessage',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $message;
        }
    }

    /**
     * Map server severity to client severity
     * @param string $serverSeverity Server severity level
     * @return string Client severity level
     */
    protected function mapServerSeverity(string $serverSeverity): string
    {
        try {
            $mapping = [
                'critical' => 'error',
                'warning' => 'warn',
                'notice' => 'info',
                'information' => 'info'
            ];

            return $mapping[$serverSeverity] ?? $serverSeverity;
        } catch (\Exception $e) {
            Log::error('Error mapping server severity', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'mapServerSeverity',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'error';
        }
    }

    /**
     * Validate error settings
     * @return void
     */
    protected function validateErrorSettings(): void
    {
        try {
            $this->errorSettings['displayType'] = $this->validateDisplayType($this->errorSettings['displayType']);
            $this->errorSettings['position'] = $this->validatePosition($this->errorSettings['position']);
            $this->errorSettings['timeout'] = max(1000, $this->errorSettings['timeout']);
        } catch (\Exception $e) {
            Log::error('Error validating error settings', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'validateErrorSettings',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get current widget ID for logging
     * @return string Widget ID
     */
    protected function getCurrentWidgetId(): string
    {
        try {
            return $this->currentState['widgetId'] ?? 'unknown';
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    /**
     * Check if widget has error handling capability
     * @return bool True if error handling is available
     */
    protected function hasErrorHandling(): bool
    {
        try {
            return !empty($this->errorConfig);
        } catch (\Exception $e) {
            Log::error('Error checking error handling capability', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'hasErrorHandling',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get current error configuration
     * @return array Current error configuration
     */
    public function getErrorConfig(): array
    {
        try {
            return $this->errorConfig;
        } catch (\Exception $e) {
            Log::error('Error getting error configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasErrorHandling.php',
                'method' => 'getErrorConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
