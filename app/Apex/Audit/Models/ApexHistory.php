<?php

/*
Copyright EXOR Group ltd 2025 
Version 1.0.0.0 
APEX Laravel Auditing
Description: Eloquent model for APEX history records that provides user-facing audit trail with rollback capabilities. Manages clean history display, field change tracking, and user permission integration for the audit system.
*/

namespace App\Apex\Audit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApexHistory extends Model
{
    protected $table = 'apex_history';

    protected $fillable = [
        'audit_id',
        'model_type',
        'model_id',
        'action_type',
        'field_changes',
        'description',
        'rollback_data',
        'can_rollback',
        'rolled_back_at',
        'rolled_back_by',
        'user_id',
    ];

    protected $casts = [
        'field_changes' => 'array',
        'rollback_data' => 'array',
        'can_rollback' => 'boolean',
        'rolled_back_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'rolled_back_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    /**
     * Get the user who rolled back this action.
     */
    public function rolledBackBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'rolled_back_by');
    }

    /**
     * Get the connection name for this model.
     */
    public function getConnectionName()
    {
        // Check if multi-tenancy is enabled
        if (!config('apex.audit.tenancy.enabled', false)) {
            return parent::getConnectionName();
        }

        // Try to detect tenant context
        if (function_exists('tenant') && tenant()) {
            // We're in tenant context, use tenant connection
            return config('database.default');
        }

        // Fallback to central connection or default
        $fallback = config('apex.audit.tenancy.fallback_behavior', 'central');

        switch ($fallback) {
            case 'central':
                return config('apex.audit.tenancy.central_connection', 'central');
            case 'skip':
                return null;
            default:
                return parent::getConnectionName();
        }
    }

    /**
     * Get the related audit record.
     * Note: We don't use Eloquent relationship since audit table has no model.
     */
    public function getAuditRecord(): ?array
    {
        if (!$this->audit_id) {
            return null;
        }

        // Use the same connection logic as the model
        $connection = $this->getConnectionName();
        $db = $connection ? DB::connection($connection) : DB::connection();

        return $db->table('apex_audit')
            ->where('id', $this->audit_id)
            ->first();
    }

    /**
     * Get the audited model instance if it still exists.
     */
    public function getAuditedModel(): ?Model
    {
        if (!$this->model_type || !$this->model_id) {
            return null;
        }

        try {
            if (!class_exists($this->model_type)) {
                return null;
            }

            return $this->model_type::find($this->model_id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if this history record has been rolled back.
     */
    public function isRolledBack(): bool
    {
        return !is_null($this->rolled_back_at);
    }

    /**
     * Check if this history record can be rolled back by the current user.
     */
    public function canBeRolledBack(): bool
    {
        // Already rolled back
        if ($this->isRolledBack()) {
            return false;
        }

        // Not configured as rollbackable
        if (!$this->can_rollback) {
            return false;
        }

        // Global rollback disabled
        if (!config('apex.audit.history.allow_rollback', true)) {
            return false;
        }

        // Check user permissions
        return $this->userCanRollback();
    }

    /**
     * Check if the current user has permission to rollback this record.
     */
    protected function userCanRollback(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $config = config('apex.audit.history.rollback_permissions', []);
        $user = Auth::user();

        if (isset($config['gate'])) {
            return $user->can($config['gate'], $this);
        }

        if (isset($config['roles']) && method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole($config['roles']);
        }

        if (isset($config['permissions']) && method_exists($user, 'hasAnyPermission')) {
            return $user->hasAnyPermission($config['permissions']);
        }

        return true;
    }

    /**
     * Get formatted field changes for display.
     */
    public function getFormattedChanges(): array
    {
        if (!$this->field_changes) {
            return [];
        }

        $formatted = [];
        foreach ($this->field_changes as $field => $change) {
            $formatted[] = [
                'field' => $field,
                'label' => $change['field_label'] ?? ucwords(str_replace('_', ' ', $field)),
                'old_value' => $this->formatValue($change['old']),
                'new_value' => $this->formatValue($change['new']),
                'old_raw' => $change['old'],
                'new_raw' => $change['new'],
            ];
        }

        return $formatted;
    }

    /**
     * Format a value for display.
     */
    protected function formatValue($value): string
    {
        if (is_null($value)) {
            return '(empty)';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        if (is_string($value) && strlen($value) > 100) {
            return substr($value, 0, 100) . '...';
        }

        return (string) $value;
    }

    /**
     * Get action type in human-readable format.
     */
    public function getActionLabel(): string
    {
        $labels = [
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'restore' => 'Restored',
        ];

        return $labels[$this->action_type] ?? ucfirst($this->action_type);
    }

    /**
     * Get action color for UI display.
     */
    public function getActionColor(): string
    {
        $colors = [
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            'restore' => 'info',
        ];

        return $colors[$this->action_type] ?? 'secondary';
    }

    /**
     * Scope to filter by model.
     */
    public function scopeForModel(Builder $query, string $modelType, $modelId = null): Builder
    {
        $query->where('model_type', $modelType);

        if ($modelId !== null) {
            $query->where('model_id', (string) $modelId);
        }

        return $query;
    }

    /**
     * Scope to filter by action type.
     */
    public function scopeByAction(Builder $query, string|array $actions): Builder
    {
        if (is_array($actions)) {
            return $query->whereIn('action_type', $actions);
        }

        return $query->where('action_type', $actions);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter rollbackable records.
     */
    public function scopeRollbackable(Builder $query): Builder
    {
        return $query->where('can_rollback', true)
            ->whereNull('rolled_back_at');
    }

    /**
     * Scope to filter rolled back records.
     */
    public function scopeRolledBack(Builder $query): Builder
    {
        return $query->whereNotNull('rolled_back_at');
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for recent records.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get history records for a specific model instance.
     */
    public static function forModelInstance(Model $model): Builder
    {
        return static::forModel(get_class($model), $model->getKey());
    }

    /**
     * Get summary statistics for history records.
     */
    public static function getStatistics(string $modelType = null, $modelId = null): array
    {
        $query = static::query();

        if ($modelType) {
            $query->forModel($modelType, $modelId);
        }

        $total = $query->count();
        $byAction = $query->select('action_type', DB::raw('count(*) as count'))
            ->groupBy('action_type')
            ->pluck('count', 'action_type')
            ->toArray();

        $rollbackable = static::rollbackable()->count();
        $rolledBack = static::rolledBack()->count();

        return [
            'total_records' => $total,
            'by_action' => $byAction,
            'rollbackable_records' => $rollbackable,
            'rolled_back_records' => $rolledBack,
            'rollback_rate' => $total > 0 ? round(($rolledBack / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Clean up old history records based on retention policy.
     */
    public static function cleanup(): int
    {
        $retentionDays = config('apex.audit.history.retention_days');

        if (!$retentionDays) {
            return 0; // No cleanup if retention is not set
        }

        $cutoffDate = now()->subDays($retentionDays);

        return static::where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Get the JSON schema for history widget display.
     */
    public static function getWidgetSchema(string $modelType, $modelId = null): array
    {
        return [
            'widget_type' => 'apex_history',
            'title' => 'Change History',
            'model_filter' => [
                'model_type' => $modelType,
                'model_id' => $modelId,
            ],
            'columns' => [
                [
                    'field' => 'action_type',
                    'label' => 'Action',
                    'type' => 'badge',
                    'color_map' => [
                        'create' => 'success',
                        'update' => 'warning',
                        'delete' => 'danger',
                        'restore' => 'info',
                    ],
                ],
                [
                    'field' => 'description',
                    'label' => 'Description',
                    'type' => 'text',
                ],
                [
                    'field' => 'user.name',
                    'label' => 'User',
                    'type' => 'relation',
                ],
                [
                    'field' => 'created_at',
                    'label' => 'Date',
                    'type' => 'datetime',
                    'format' => config('apex.audit.history.ui.date_format', 'Y-m-d H:i:s'),
                ],
            ],
            'actions' => [
                'view_changes' => [
                    'label' => 'View Changes',
                    'icon' => 'eye',
                    'type' => 'modal',
                ],
                'rollback' => [
                    'label' => 'Rollback',
                    'icon' => 'undo',
                    'type' => 'action',
                    'confirm' => true,
                    'permission' => 'apex.history.rollback',
                    'condition' => 'can_rollback && !rolled_back_at',
                ],
            ],
            'pagination' => [
                'per_page' => config('apex.audit.history.ui.items_per_page', 20),
            ],
            'filters' => [
                'action_type' => [
                    'type' => 'select',
                    'options' => ['create', 'update', 'delete', 'restore'],
                ],
                'user_id' => [
                    'type' => 'user_select',
                ],
                'date_range' => [
                    'type' => 'date_range',
                ],
            ],
        ];
    }
}
