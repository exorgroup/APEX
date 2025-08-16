<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Parameter injection trait for PRO widgets providing dynamic parameter resolution using template syntax like {{widget:id.value}}, {{form:*}}, {{user:property}}
 * File location: app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits/HasParameterInjection.php
 */

namespace App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits;

use App\Apex\Pro\Widget\PrimeVueBaseWidget\Services\ParameterInjector;
use Illuminate\Support\Facades\Log;

trait HasParameterInjection
{
    /**
     * Parameter configuration storage
     * @var array
     */
    protected array $parameterConfig = [];

    /**
     * Parameter injector instance
     * @var ParameterInjector|null
     */
    protected ?ParameterInjector $parameterInjector = null;

    /**
     * Available parameter contexts
     * @var array
     */
    protected array $parameterContexts = [
        'widget' => 'Widget values from other widgets',
        'form' => 'Form data and state',
        'user' => 'User session and profile data',
        'static' => 'Static literal values',
        'config' => 'Application configuration values',
        'route' => 'Current route parameters'
    ];

    /**
     * Initialize parameter injection system
     * @param array $config Widget configuration containing parameter definitions
     * @return void
     */
    protected function initializeParameterInjection(array $config): void
    {
        try {
            if (isset($config['parameterConfig'])) {
                $this->parameterConfig = $config['parameterConfig'];
                $this->parameterInjector = new ParameterInjector();
                $this->registerParameterContexts();
            }
        } catch (\Exception $e) {
            Log::error('Error initializing parameter injection', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'initializeParameterInjection',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get parameter schema for widget validation
     * @return array JSON schema definition for parameters
     */
    protected function getParameterSchema(): array
    {
        try {
            return [
                'type' => 'object',
                'description' => 'Parameter injection configuration',
                'properties' => [
                    'contexts' => [
                        'type' => 'array',
                        'description' => 'Available parameter contexts',
                        'items' => [
                            'type' => 'string',
                            'enum' => array_keys($this->parameterContexts)
                        ]
                    ],
                    'templates' => [
                        'type' => 'object',
                        'description' => 'Parameter templates for injection',
                        'patternProperties' => [
                            '^.*$' => [
                                'type' => 'string',
                                'pattern' => '^\{\{(widget|form|user|static|config|route):[^}]+\}\}$',
                                'description' => 'Parameter template like {{widget:id.value}}'
                            ]
                        ]
                    ],
                    'validation' => [
                        'type' => 'object',
                        'description' => 'Parameter validation rules',
                        'properties' => [
                            'required' => [
                                'type' => 'array',
                                'description' => 'Required parameters',
                                'items' => ['type' => 'string']
                            ],
                            'types' => [
                                'type' => 'object',
                                'description' => 'Parameter type validation',
                                'patternProperties' => [
                                    '^.*$' => [
                                        'type' => 'string',
                                        'enum' => ['string', 'number', 'boolean', 'array', 'object']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting parameter schema', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'getParameterSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Process parameter configuration and setup injection templates
     * @param array $parameterConfig Raw parameter configuration from JSON
     * @return array Processed parameter configuration
     */
    protected function processParameterConfig(array $parameterConfig): array
    {
        try {
            $processedConfig = [
                'contexts' => $parameterConfig['contexts'] ?? array_keys($this->parameterContexts),
                'templates' => $this->validateParameterTemplates($parameterConfig['templates'] ?? []),
                'validation' => $parameterConfig['validation'] ?? [],
                'cache' => $parameterConfig['cache'] ?? true,
                'watchChanges' => $parameterConfig['watchChanges'] ?? true
            ];

            return $processedConfig;
        } catch (\Exception $e) {
            Log::error('Error processing parameter configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'processParameterConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $parameterConfig;
        }
    }

    /**
     * Validate parameter templates syntax
     * @param array $templates Parameter templates to validate
     * @return array Validated and processed templates
     */
    protected function validateParameterTemplates(array $templates): array
    {
        try {
            $validatedTemplates = [];

            foreach ($templates as $key => $template) {
                if ($this->isValidParameterTemplate($template)) {
                    $validatedTemplates[$key] = [
                        'template' => $template,
                        'context' => $this->extractParameterContext($template),
                        'path' => $this->extractParameterPath($template),
                        'compiled' => $this->compileParameterTemplate($template)
                    ];
                } else {
                    Log::warning('Invalid parameter template', [
                        'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                        'file' => 'HasParameterInjection.php',
                        'method' => 'validateParameterTemplates',
                        'template' => $template,
                        'key' => $key
                    ]);
                }
            }

            return $validatedTemplates;
        } catch (\Exception $e) {
            Log::error('Error validating parameter templates', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'validateParameterTemplates',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $templates;
        }
    }

    /**
     * Check if parameter template has valid syntax
     * @param string $template Parameter template to validate
     * @return bool True if template syntax is valid
     */
    protected function isValidParameterTemplate(string $template): bool
    {
        try {
            // Check if template matches pattern {{context:path}}
            $pattern = '/^\{\{(widget|form|user|static|config|route):[^}]+\}\}$/';
            return preg_match($pattern, $template) === 1;
        } catch (\Exception $e) {
            Log::error('Error validating parameter template', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'isValidParameterTemplate',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Extract context from parameter template
     * @param string $template Parameter template like {{widget:id.value}}
     * @return string Context name (widget, form, user, etc.)
     */
    protected function extractParameterContext(string $template): string
    {
        try {
            // Extract context from {{context:path}}
            if (preg_match('/^\{\{([^:]+):/', $template, $matches)) {
                return $matches[1];
            }
            return '';
        } catch (\Exception $e) {
            Log::error('Error extracting parameter context', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'extractParameterContext',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return '';
        }
    }

    /**
     * Extract path from parameter template
     * @param string $template Parameter template like {{widget:id.value}}
     * @return string Path portion (id.value)
     */
    protected function extractParameterPath(string $template): string
    {
        try {
            // Extract path from {{context:path}}
            if (preg_match('/^\{\{[^:]+:([^}]+)\}\}$/', $template, $matches)) {
                return $matches[1];
            }
            return '';
        } catch (\Exception $e) {
            Log::error('Error extracting parameter path', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'extractParameterPath',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return '';
        }
    }

    /**
     * Compile parameter template for efficient resolution
     * @param string $template Parameter template to compile
     * @return array Compiled template structure
     */
    protected function compileParameterTemplate(string $template): array
    {
        try {
            $context = $this->extractParameterContext($template);
            $path = $this->extractParameterPath($template);

            // Split path into segments for nested access
            $pathSegments = explode('.', $path);

            return [
                'context' => $context,
                'path' => $path,
                'segments' => $pathSegments,
                'isWildcard' => $path === '*',
                'isArray' => str_contains($path, '[]'),
                'template' => $template
            ];
        } catch (\Exception $e) {
            Log::error('Error compiling parameter template', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'compileParameterTemplate',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Register parameter contexts with the injector
     * @return void
     */
    protected function registerParameterContexts(): void
    {
        try {
            if ($this->parameterInjector) {
                foreach ($this->parameterContexts as $context => $description) {
                    $this->parameterInjector->registerContext($context, $description);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error registering parameter contexts', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'registerParameterContexts',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Resolve parameter template to actual value
     * @param string $template Parameter template to resolve
     * @param array $contextData Available context data
     * @return mixed Resolved parameter value
     */
    public function resolveParameter(string $template, array $contextData = []): mixed
    {
        try {
            if ($this->parameterInjector) {
                return $this->parameterInjector->resolve($template, $contextData);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error resolving parameter', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'resolveParameter',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Check if widget has parameter injection capability
     * @return bool True if parameter injection is available
     */
    protected function hasParameterInjection(): bool
    {
        try {
            return !empty($this->parameterConfig) && $this->parameterInjector !== null;
        } catch (\Exception $e) {
            Log::error('Error checking parameter injection capability', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'hasParameterInjection',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get available parameter contexts
     * @return array Available parameter contexts with descriptions
     */
    public function getAvailableParameterContexts(): array
    {
        try {
            return $this->parameterContexts;
        } catch (\Exception $e) {
            Log::error('Error getting available parameter contexts', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'getAvailableParameterContexts',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get current parameter configuration
     * @return array Current parameter configuration
     */
    public function getParameterConfig(): array
    {
        try {
            return $this->parameterConfig;
        } catch (\Exception $e) {
            Log::error('Error getting parameter configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasParameterInjection.php',
                'method' => 'getParameterConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
