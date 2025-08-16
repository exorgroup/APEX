<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Parameter injector service for PRO widgets providing server-side parameter template resolution, context management and dynamic value injection for {{widget:id.value}} syntax
 * File location: app/Apex/Pro/Widget/PrimeVueBaseWidget/Services/ParameterInjector.php
 */

namespace App\Apex\Pro\Widget\PrimeVueBaseWidget\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

class ParameterInjector
{
    /**
     * Available parameter contexts
     * @var array
     */
    protected array $contexts = [];

    /**
     * Context data providers
     * @var array
     */
    protected array $contextProviders = [];

    /**
     * Compiled templates cache
     * @var array
     */
    protected array $compiledTemplates = [];

    /**
     * Configuration settings
     * @var array
     */
    protected array $config = [
        'enableCaching' => true,
        'cacheDuration' => 3600,
        'maxNestingLevel' => 10,
        'strictMode' => false,
        'defaultValues' => [],
        'contextValidation' => true
    ];

    /**
     * Supported context types
     * @var array
     */
    protected array $supportedContexts = [
        'widget' => 'Widget values from other widgets',
        'form' => 'Form data and state',
        'user' => 'User session and profile data',
        'static' => 'Static literal values',
        'config' => 'Application configuration values',
        'route' => 'Current route parameters'
    ];

    /**
     * Template pattern for parameter matching
     * @var string
     */
    protected string $templatePattern = '/\{\{([^:]+):([^}]+)\}\}/';

    /**
     * Constructor
     * @param array $config Configuration options
     */
    public function __construct(array $config = [])
    {
        try {
            $this->config = array_merge($this->config, $config);
            $this->initializeContextProviders();
            $this->registerDefaultContexts();
        } catch (\Exception $e) {
            Log::error('Error initializing ParameterInjector', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => '__construct',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Register parameter context
     * @param string $context Context name
     * @param string $description Context description
     * @return bool Success status
     */
    public function registerContext(string $context, string $description): bool
    {
        try {
            if (!$this->isValidContextName($context)) {
                Log::warning('Invalid context name', [
                    'context' => $context,
                    'description' => $description
                ]);
                return false;
            }

            $this->contexts[$context] = [
                'name' => $context,
                'description' => $description,
                'registered' => now()->toISOString(),
                'provider' => $this->contextProviders[$context] ?? null
            ];

            Log::info('Parameter context registered', [
                'context' => $context,
                'description' => $description
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error registering parameter context', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'registerContext',
                'context' => $context,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Resolve parameter template to actual value
     * @param string $template Parameter template (e.g., {{widget:id.value}})
     * @param array $contextData Available context data
     * @return mixed Resolved parameter value
     */
    public function resolve(string $template, array $contextData = []): mixed
    {
        try {
            // Check cache first
            if ($this->config['enableCaching']) {
                $cacheKey = $this->getCacheKey($template, $contextData);
                $cached = Cache::get($cacheKey);
                if ($cached !== null) {
                    return $cached;
                }
            }

            // Validate template format
            if (!$this->isValidTemplate($template)) {
                Log::warning('Invalid parameter template', [
                    'template' => $template
                ]);
                return $this->getDefaultValue($template);
            }

            // Compile template if not cached
            $compiled = $this->compileTemplate($template);
            if (!$compiled) {
                return $this->getDefaultValue($template);
            }

            // Resolve parameter value
            $resolvedValue = $this->resolveCompiledTemplate($compiled, $contextData);

            // Cache result if enabled
            if ($this->config['enableCaching']) {
                $cacheKey = $this->getCacheKey($template, $contextData);
                Cache::put($cacheKey, $resolvedValue, $this->config['cacheDuration']);
            }

            return $resolvedValue;
        } catch (\Exception $e) {
            Log::error('Error resolving parameter', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'resolve',
                'template' => $template,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getDefaultValue($template);
        }
    }

    /**
     * Resolve multiple parameter templates
     * @param array $templates Array of parameter templates
     * @param array $contextData Available context data
     * @return array Resolved parameter values
     */
    public function resolveMultiple(array $templates, array $contextData = []): array
    {
        try {
            $resolved = [];

            foreach ($templates as $key => $template) {
                if (is_string($template)) {
                    $resolved[$key] = $this->resolve($template, $contextData);
                } else {
                    // Static value
                    $resolved[$key] = $template;
                }
            }

            return $resolved;
        } catch (\Exception $e) {
            Log::error('Error resolving multiple parameters', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'resolveMultiple',
                'templateCount' => count($templates),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Process string with embedded parameter templates
     * @param string $text Text containing parameter templates
     * @param array $contextData Available context data
     * @return string Processed text with resolved parameters
     */
    public function processString(string $text, array $contextData = []): string
    {
        try {
            return preg_replace_callback(
                $this->templatePattern,
                function ($matches) use ($contextData) {
                    $template = $matches[0]; // Full match: {{context:path}}
                    $resolved = $this->resolve($template, $contextData);

                    // Convert to string representation
                    return $this->valueToString($resolved);
                },
                $text
            );
        } catch (\Exception $e) {
            Log::error('Error processing string with parameters', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'processString',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $text;
        }
    }

    /**
     * Validate parameter template format
     * @param string $template Template to validate
     * @return bool True if template is valid
     */
    protected function isValidTemplate(string $template): bool
    {
        try {
            $pattern = '/^\{\{(widget|form|user|static|config|route):[^}]+\}\}$/';
            return preg_match($pattern, $template) === 1;
        } catch (\Exception $e) {
            Log::error('Error validating template', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'isValidTemplate',
                'template' => $template,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Compile parameter template
     * @param string $template Template to compile
     * @return array|null Compiled template structure
     */
    protected function compileTemplate(string $template): ?array
    {
        try {
            // Check if already compiled
            if (isset($this->compiledTemplates[$template])) {
                return $this->compiledTemplates[$template];
            }

            if (preg_match($this->templatePattern, $template, $matches)) {
                $context = $matches[1];
                $path = $matches[2];
                $segments = explode('.', $path);

                $compiled = [
                    'template' => $template,
                    'context' => $context,
                    'path' => $path,
                    'segments' => $segments,
                    'isWildcard' => $path === '*',
                    'isArray' => str_contains($path, '[]'),
                    'nestingLevel' => count($segments),
                    'defaultValue' => $this->getDefaultValue($template)
                ];

                // Cache compiled template
                $this->compiledTemplates[$template] = $compiled;

                return $compiled;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error compiling template', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'compileTemplate',
                'template' => $template,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Resolve compiled template
     * @param array $compiled Compiled template structure
     * @param array $contextData Available context data
     * @return mixed Resolved value
     */
    protected function resolveCompiledTemplate(array $compiled, array $contextData): mixed
    {
        try {
            // Check nesting level
            if ($compiled['nestingLevel'] > $this->config['maxNestingLevel']) {
                Log::warning('Template nesting level exceeded', [
                    'template' => $compiled['template'],
                    'nestingLevel' => $compiled['nestingLevel'],
                    'maxLevel' => $this->config['maxNestingLevel']
                ]);
                return $compiled['defaultValue'];
            }

            // Get context data
            $contextValue = $this->getContextData($compiled['context'], $contextData);

            if ($contextValue === null) {
                return $compiled['defaultValue'];
            }

            // Handle wildcard (return entire context)
            if ($compiled['isWildcard']) {
                return $contextValue;
            }

            // Resolve nested path
            return $this->resolvePath($contextValue, $compiled['segments'], $compiled['defaultValue']);
        } catch (\Exception $e) {
            Log::error('Error resolving compiled template', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'resolveCompiledTemplate',
                'template' => $compiled['template'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $compiled['defaultValue'] ?? null;
        }
    }

    /**
     * Get context data for specified context
     * @param string $context Context name
     * @param array $contextData Provided context data
     * @return mixed Context data or null
     */
    protected function getContextData(string $context, array $contextData): mixed
    {
        try {
            // First check provided context data
            if (isset($contextData[$context])) {
                return $contextData[$context];
            }

            // Use context provider if available
            if (isset($this->contextProviders[$context])) {
                return $this->contextProviders[$context]();
            }

            // Fallback to default context handlers
            return match ($context) {
                'user' => $this->getUserContext(),
                'config' => $this->getConfigContext(),
                'route' => $this->getRouteContext(),
                'static' => $this->getStaticContext(),
                default => null
            };
        } catch (\Exception $e) {
            Log::error('Error getting context data', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'getContextData',
                'context' => $context,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Resolve nested path in data
     * @param mixed $data Data to traverse
     * @param array $segments Path segments
     * @param mixed $defaultValue Default value if path not found
     * @return mixed Resolved value
     */
    protected function resolvePath(mixed $data, array $segments, mixed $defaultValue): mixed
    {
        try {
            $current = $data;

            foreach ($segments as $segment) {
                // Handle array notation
                if (str_contains($segment, '[]')) {
                    $arrayKey = str_replace('[]', '', $segment);

                    if (is_array($current) && isset($current[$arrayKey])) {
                        $current = $current[$arrayKey];
                        // If it's an array, return the array itself
                        if (is_array($current)) {
                            return $current;
                        }
                    } else {
                        return $defaultValue;
                    }
                } else {
                    // Regular property access
                    if (is_array($current) && isset($current[$segment])) {
                        $current = $current[$segment];
                    } elseif (is_object($current) && property_exists($current, $segment)) {
                        $current = $current->{$segment};
                    } elseif (is_object($current) && method_exists($current, $segment)) {
                        $current = $current->{$segment}();
                    } else {
                        return $defaultValue;
                    }
                }
            }

            return $current;
        } catch (\Exception $e) {
            Log::error('Error resolving path', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'resolvePath',
                'segments' => $segments,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $defaultValue;
        }
    }

    /**
     * Get user context data
     * @return array User context data
     */
    protected function getUserContext(): array
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                return [
                    'id' => $user->id,
                    'name' => $user->name ?? '',
                    'email' => $user->email ?? '',
                    'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [],
                    'permissions' => method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name')->toArray() : [],
                    'profile' => method_exists($user, 'profile') ? $user->profile?->toArray() : [],
                    'preferences' => method_exists($user, 'preferences') ? $user->preferences?->toArray() : [],
                    'lastLogin' => $user->last_login_at ?? null,
                    'createdAt' => $user->created_at ?? null
                ];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error getting user context', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'getUserContext',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get configuration context data
     * @return array Configuration context data
     */
    protected function getConfigContext(): array
    {
        try {
            return [
                'app' => [
                    'name' => Config::get('app.name'),
                    'env' => Config::get('app.env'),
                    'debug' => Config::get('app.debug'),
                    'url' => Config::get('app.url'),
                    'timezone' => Config::get('app.timezone'),
                    'locale' => Config::get('app.locale')
                ],
                'database' => [
                    'default' => Config::get('database.default'),
                    'connections' => array_keys(Config::get('database.connections', []))
                ],
                'cache' => [
                    'default' => Config::get('cache.default')
                ],
                'mail' => [
                    'default' => Config::get('mail.default'),
                    'from' => Config::get('mail.from')
                ],
                'session' => [
                    'driver' => Config::get('session.driver'),
                    'lifetime' => Config::get('session.lifetime')
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting config context', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'getConfigContext',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get route context data
     * @return array Route context data
     */
    protected function getRouteContext(): array
    {
        try {
            $route = Route::current();

            if (!$route) {
                return [];
            }

            return [
                'name' => $route->getName(),
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'parameters' => $route->parameters(),
                'action' => $route->getAction(),
                'controller' => $route->getController() ? get_class($route->getController()) : null,
                'middleware' => $route->middleware(),
                'domain' => $route->domain(),
                'prefix' => $route->getPrefix(),
                'compiled' => $route->getCompiled()?->getStaticPrefix(),
                'where' => $route->wheres
            ];
        } catch (\Exception $e) {
            Log::error('Error getting route context', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'getRouteContext',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get static context data
     * @return array Static context data
     */
    protected function getStaticContext(): array
    {
        try {
            return $this->config['defaultValues'] ?? [];
        } catch (\Exception $e) {
            Log::error('Error getting static context', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'getStaticContext',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Convert value to string representation
     * @param mixed $value Value to convert
     * @return string String representation
     */
    protected function valueToString(mixed $value): string
    {
        try {
            if ($value === null) {
                return '';
            }

            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }

            if (is_scalar($value)) {
                return (string) $value;
            }

            if (is_array($value) || is_object($value)) {
                return json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            return '';
        } catch (\Exception $e) {
            Log::error('Error converting value to string', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'valueToString',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return '';
        }
    }

    /**
     * Get default value for template
     * @param string $template Template to get default for
     * @return mixed Default value
     */
    protected function getDefaultValue(string $template): mixed
    {
        try {
            // Check if specific default value is configured
            if (isset($this->config['defaultValues'][$template])) {
                return $this->config['defaultValues'][$template];
            }

            // Return null as default
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting default value', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'getDefaultValue',
                'template' => $template,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get cache key for template and context
     * @param string $template Parameter template
     * @param array $contextData Context data
     * @return string Cache key
     */
    protected function getCacheKey(string $template, array $contextData): string
    {
        try {
            $contextHash = md5(serialize($contextData));
            return "param_inject_{$template}_{$contextHash}";
        } catch (\Exception $e) {
            Log::error('Error generating cache key', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'getCacheKey',
                'template' => $template,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return "param_inject_{$template}_" . time();
        }
    }

    /**
     * Validate context name
     * @param string $context Context name to validate
     * @return bool True if valid
     */
    protected function isValidContextName(string $context): bool
    {
        try {
            return preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $context) === 1;
        } catch (\Exception $e) {
            Log::error('Error validating context name', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'isValidContextName',
                'context' => $context,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Initialize context providers
     * @return void
     */
    protected function initializeContextProviders(): void
    {
        try {
            $this->contextProviders = [
                'user' => fn() => $this->getUserContext(),
                'config' => fn() => $this->getConfigContext(),
                'route' => fn() => $this->getRouteContext(),
                'static' => fn() => $this->getStaticContext()
            ];
        } catch (\Exception $e) {
            Log::error('Error initializing context providers', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'initializeContextProviders',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Register default contexts
     * @return void
     */
    protected function registerDefaultContexts(): void
    {
        try {
            foreach ($this->supportedContexts as $context => $description) {
                $this->registerContext($context, $description);
            }
        } catch (\Exception $e) {
            Log::error('Error registering default contexts', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'registerDefaultContexts',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Set context provider
     * @param string $context Context name
     * @param callable $provider Provider function
     * @return bool Success status
     */
    public function setContextProvider(string $context, callable $provider): bool
    {
        try {
            if (!$this->isValidContextName($context)) {
                return false;
            }

            $this->contextProviders[$context] = $provider;

            Log::info('Context provider set', [
                'context' => $context
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error setting context provider', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'setContextProvider',
                'context' => $context,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get registered contexts
     * @return array Registered contexts
     */
    public function getRegisteredContexts(): array
    {
        try {
            return $this->contexts;
        } catch (\Exception $e) {
            Log::error('Error getting registered contexts', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'getRegisteredContexts',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Clear parameter cache
     * @return bool Success status
     */
    public function clearCache(): bool
    {
        try {
            Cache::flush();
            $this->compiledTemplates = [];

            Log::info('Parameter injection cache cleared');

            return true;
        } catch (\Exception $e) {
            Log::error('Error clearing parameter cache', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
                'method' => 'clearCache',
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

            Log::info('Parameter injector configuration updated', [
                'config' => $newConfig
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Services',
                'file' => 'ParameterInjector.php',
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
                'file' => 'ParameterInjector.php',
                'method' => 'getConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
