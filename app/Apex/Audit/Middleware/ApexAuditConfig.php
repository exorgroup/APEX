<?php

/*
Copyright EXOR Group ltd 2025 
Version 1.0.0.0 
APEX Laravel Auditing
Description: Middleware for configuring audit settings per route or request. Allows fine-grained control over audit behavior including field tracking, source identification, and additional context data.
*/

namespace App\Apex\Audit\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApexAuditConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$configParams): Response
    {
        // Parse configuration parameters
        $auditConfig = $this->parseConfigParameters($configParams);

        // Store configuration for this request
        app()->instance('apex.audit.request.config', $auditConfig);

        // Log the configuration application if debug mode is enabled
        if (config('app.debug') && !empty($auditConfig)) {
            Log::debug('APEX Audit: Route-level configuration applied', [
                'route' => $request->route()?->getName() ?? $request->path(),
                'config' => $auditConfig,
            ]);
        }

        $response = $next($request);

        // Clean up configuration after request
        app()->forgetInstance('apex.audit.request.config');

        return $response;
    }

    /**
     * Parse configuration parameters from middleware.
     * 
     * @param array $configParams
     * @return array
     */
    protected function parseConfigParameters(array $configParams): array
    {
        $config = [];

        foreach ($configParams as $param) {
            // Handle different parameter formats
            if (is_string($param)) {
                $this->parseStringParameter($param, $config);
            } elseif (is_array($param)) {
                $config = array_merge($config, $param);
            }
        }

        return $config;
    }

    /**
     * Parse a string parameter into configuration.
     * 
     * @param string $param
     * @param array &$config
     */
    protected function parseStringParameter(string $param, array &$config): void
    {
        // Handle key=value format
        if (strpos($param, '=') !== false) {
            [$key, $value] = explode('=', $param, 2);
            $this->setConfigValue($config, trim($key), $this->parseValue(trim($value)));
            return;
        }

        // Handle JSON format
        if ($this->isJson($param)) {
            $decoded = json_decode($param, true);
            if ($decoded) {
                $config = array_merge($config, $decoded);
            }
            return;
        }

        // Handle comma-separated key:value pairs
        if (strpos($param, ':') !== false) {
            $pairs = explode(',', $param);
            foreach ($pairs as $pair) {
                if (strpos($pair, ':') !== false) {
                    [$key, $value] = explode(':', $pair, 2);
                    $this->setConfigValue($config, trim($key), $this->parseValue(trim($value)));
                }
            }
            return;
        }

        // Handle simple flags
        $config[$param] = true;
    }

    /**
     * Set a configuration value, handling nested keys.
     * 
     * @param array &$config
     * @param string $key
     * @param mixed $value
     */
    protected function setConfigValue(array &$config, string $key, $value): void
    {
        // Handle nested keys like "additional_data.api_version"
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $current = &$config;

            foreach ($keys as $nestedKey) {
                if (!isset($current[$nestedKey])) {
                    $current[$nestedKey] = [];
                }
                $current = &$current[$nestedKey];
            }

            $current = $value;
        } else {
            $config[$key] = $value;
        }
    }

    /**
     * Parse a string value to appropriate type.
     * 
     * @param string $value
     * @return mixed
     */
    protected function parseValue(string $value)
    {
        // Handle boolean values
        if (in_array(strtolower($value), ['true', 'false'])) {
            return strtolower($value) === 'true';
        }

        // Handle numeric values
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float) $value : (int) $value;
        }

        // Handle JSON arrays/objects
        if ($this->isJson($value)) {
            return json_decode($value, true);
        }

        // Handle comma-separated lists
        if (strpos($value, ',') !== false && !$this->isJson($value)) {
            return array_map('trim', explode(',', $value));
        }

        // Return as string
        return $value;
    }

    /**
     * Check if a string is valid JSON.
     * 
     * @param string $string
     * @return bool
     */
    protected function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Create middleware configuration for common scenarios.
     */
    public static function configs(): array
    {
        return [
            // API endpoints
            'api' => [
                'source_context' => 'api',
                'additional_data' => ['interface' => 'api'],
            ],

            // Admin panel
            'admin' => [
                'source_context' => 'admin',
                'track_all_fields' => true,
                'additional_data' => ['interface' => 'admin'],
            ],

            // Public interface
            'public' => [
                'source_context' => 'public',
                'track_minimal' => true,
                'additional_data' => ['interface' => 'public'],
            ],

            // Sensitive operations
            'sensitive' => [
                'enhanced_tracking' => true,
                'require_justification' => true,
                'additional_data' => ['sensitivity_level' => 'high'],
            ],

            // Bulk operations
            'bulk' => [
                'batch_tracking' => true,
                'track_summary_only' => true,
                'additional_data' => ['operation_type' => 'bulk'],
            ],

            // No tracking
            'no_audit' => [
                'disable_audit' => true,
            ],

            // History only (no detailed audit)
            'history_only' => [
                'disable_detailed_audit' => true,
                'history_only' => true,
            ],
        ];
    }

    /**
     * Get configuration for a specific preset.
     * 
     * @param string $preset
     * @return array
     */
    public static function preset(string $preset): array
    {
        $configs = static::configs();
        return $configs[$preset] ?? [];
    }

    /**
     * Create middleware instance with preset configuration.
     * 
     * @param string $preset
     * @param array $additional
     * @return string
     */
    public static function withPreset(string $preset, array $additional = []): string
    {
        $config = array_merge(static::preset($preset), $additional);
        return 'apex.audit.config:' . json_encode($config);
    }

    /**
     * Create middleware for tracking specific fields only.
     * 
     * @param array $fields
     * @return string
     */
    public static function trackFields(array $fields): string
    {
        return 'apex.audit.config:track_fields=' . implode(',', $fields);
    }

    /**
     * Create middleware for excluding specific fields.
     * 
     * @param array $fields
     * @return string
     */
    public static function excludeFields(array $fields): string
    {
        return 'apex.audit.config:exclude_fields=' . implode(',', $fields);
    }

    /**
     * Create middleware for tracking specific actions only.
     * 
     * @param array $actions
     * @return string
     */
    public static function trackActions(array $actions): string
    {
        return 'apex.audit.config:audit_events=' . implode(',', $actions);
    }

    /**
     * Create middleware with custom source element.
     * 
     * @param string $element
     * @return string
     */
    public static function sourceElement(string $element): string
    {
        return "apex.audit.config:source_element={$element}";
    }

    /**
     * Create middleware with additional context data.
     * 
     * @param array $data
     * @return string
     */
    public static function withContext(array $data): string
    {
        return 'apex.audit.config:additional_data=' . json_encode($data);
    }

    /**
     * Disable auditing for this route.
     * 
     * @return string
     */
    public static function disabled(): string
    {
        return 'apex.audit.config:disable_audit=true';
    }

    /**
     * Enable enhanced tracking with full context.
     * 
     * @return string
     */
    public static function enhanced(): string
    {
        return 'apex.audit.config:enhanced_tracking=true,track_all_fields=true';
    }
}

/*
Usage Examples:

// In routes/web.php or routes/api.php

// Using preset configurations
Route::middleware(['apex.audit.config:api'])->group(function () {
    Route::apiResource('cars', CarController::class);
});

// Using helper methods
Route::middleware([ApexAuditConfig::trackFields(['price', 'status'])])
    ->put('cars/{car}/price', [CarController::class, 'updatePrice']);

Route::middleware([ApexAuditConfig::sourceElement('admin-panel')])
    ->group(function () {
        // Admin routes
    });

// Using JSON configuration
Route::middleware(['apex.audit.config:{"source_context":"api","track_fields":["name","email"]}'])
    ->post('users', [UserController::class, 'store']);

// Using key=value pairs
Route::middleware(['apex.audit.config:source_element=user-profile,enhanced_tracking=true'])
    ->put('profile', [ProfileController::class, 'update']);

// Disable auditing for specific routes
Route::middleware([ApexAuditConfig::disabled()])
    ->get('health-check', [HealthController::class, 'check']);

// Multiple configurations
Route::middleware([
    'apex.audit.config:source_context=admin',
    'apex.audit.config:additional_data.admin_level=super',
    'apex.audit.config:track_all_fields=true'
])->group(function () {
    // Super admin routes
});
*/