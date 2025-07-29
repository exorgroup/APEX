<?php

/*
Copyright EXOR Group ltd 2025 
Version 1.0.0.0 
APEX Laravel Auditing
Description: Service for handling rollback operations in APEX auditing system. Provides safe restoration of previous model states with validation, permission checking, and audit trail creation for rollback actions.
*/

namespace App\Apex\Audit\Services;

use App\Apex\Audit\Models\ApexHistory;
use App\Apex\Audit\Exceptions\RollbackException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RollbackService
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Rollback a history record.
     * 
     * @param int $historyId History record ID to rollback
     * @return bool True if rollback was successful
     * @throws RollbackException If rollback fails
     */
    public function rollback(int $historyId): bool
    {
        $history = ApexHistory::findOrFail($historyId);

        // Validate rollback is possible
        $this->validateRollback($history);

        try {
            return DB::transaction(function () use ($history) {
                // Perform the actual rollback
                $success = $this->performRollback($history);

                if ($success) {
                    // Mark as rolled back
                    $this->markAsRolledBack($history);

                    // Log the rollback action
                    $this->logRollbackAction($history);
                }

                return $success;
            });
        } catch (\Exception $e) {
            Log::error('APEX Audit: Rollback failed', [
                'history_id' => $historyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new RollbackException("Rollback failed: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Validate that a rollback can be performed.
     * 
     * @param ApexHistory $history
     * @throws RollbackException
     */
    protected function validateRollback(ApexHistory $history): void
    {
        // Check if already rolled back
        if ($history->isRolledBack()) {
            throw new RollbackException('This action has already been rolled back.');
        }

        // Check if rollback is allowed for this record
        if (!$history->can_rollback) {
            throw new RollbackException('This action cannot be rolled back.');
        }

        // Check if rollback is globally enabled
        if (!config('apex.audit.history.allow_rollback', true)) {
            throw new RollbackException('Rollback functionality is disabled.');
        }

        // Check user permissions
        if (!$history->canBeRolledBack()) {
            throw new RollbackException('You do not have permission to rollback this action.');
        }

        // Check if rollback data exists
        if (!$history->rollback_data) {
            throw new RollbackException('No rollback data available for this action.');
        }

        // Check if model still exists (for updates)
        if ($history->action_type === 'update') {
            $model = $history->getAuditedModel();
            if (!$model) {
                throw new RollbackException('The original record no longer exists and cannot be updated.');
            }
        }
    }

    /**
     * Perform the actual rollback operation.
     * 
     * @param ApexHistory $history
     * @return bool
     * @throws RollbackException
     */
    protected function performRollback(ApexHistory $history): bool
    {
        $rollbackData = $history->rollback_data;

        switch ($rollbackData['action']) {
            case 'restore_values':
                return $this->rollbackUpdate($history, $rollbackData);

            case 'restore_record':
                return $this->rollbackDelete($history, $rollbackData);

            default:
                throw new RollbackException("Unknown rollback action: {$rollbackData['action']}");
        }
    }

    /**
     * Rollback an update operation by restoring previous values.
     * 
     * @param ApexHistory $history
     * @param array $rollbackData
     * @return bool
     * @throws RollbackException
     */
    protected function rollbackUpdate(ApexHistory $history, array $rollbackData): bool
    {
        $model = $history->getAuditedModel();

        if (!$model) {
            throw new RollbackException('Model not found for rollback update.');
        }

        $previousValues = $rollbackData['values'];
        $changedFields = $rollbackData['changed_fields'] ?? array_keys($previousValues);

        // Validate that we have permission to update these fields
        if (method_exists($model, 'getAuditableFields')) {
            $auditableFields = $model->getAuditableFields();
            $invalidFields = array_diff($changedFields, $auditableFields);

            if (!empty($invalidFields)) {
                throw new RollbackException('Cannot rollback non-auditable fields: ' . implode(', ', $invalidFields));
            }
        }

        // Store current values before rollback
        $currentValues = $model->only($changedFields);

        // Apply the rollback values
        foreach ($previousValues as $field => $value) {
            if (in_array($field, $changedFields)) {
                $model->$field = $value;
            }
        }

        // Save the model (this will trigger audit observers)
        $saved = $model->save();

        if (!$saved) {
            throw new RollbackException('Failed to save model during rollback.');
        }

        return true;
    }

    /**
     * Rollback a delete operation by restoring the record.
     * 
     * @param ApexHistory $history
     * @param array $rollbackData
     * @return bool
     * @throws RollbackException
     */
    protected function rollbackDelete(ApexHistory $history, array $rollbackData): bool
    {
        $modelClass = $history->model_type;
        $modelId = $history->model_id;
        $restoredValues = $rollbackData['values'];

        if (!class_exists($modelClass)) {
            throw new RollbackException("Model class {$modelClass} does not exist.");
        }

        // Check if record already exists (might have been restored manually)
        $existingModel = $modelClass::find($modelId);
        if ($existingModel) {
            throw new RollbackException('Record already exists and cannot be restored.');
        }

        // Try soft delete restore first if the model supports it
        if (method_exists($modelClass, 'withTrashed')) {
            $trashedModel = $modelClass::withTrashed()->find($modelId);
            if ($trashedModel && $trashedModel->trashed()) {
                $restored = $trashedModel->restore();
                if ($restored) {
                    return true;
                }
            }
        }

        // Hard restore by recreating the record
        try {
            $newModel = new $modelClass();

            // Set the primary key if it was provided
            if (isset($restoredValues[$newModel->getKeyName()])) {
                $newModel->{$newModel->getKeyName()} = $restoredValues[$newModel->getKeyName()];
            }

            // Fill with restored values
            $fillableValues = array_intersect_key($restoredValues, array_flip($newModel->getFillable()));
            $newModel->fill($fillableValues);

            // Set timestamps if they exist
            if ($newModel->usesTimestamps()) {
                $newModel->created_at = $restoredValues['created_at'] ?? now();
                $newModel->updated_at = $restoredValues['updated_at'] ?? now();
            }

            $saved = $newModel->save();

            if (!$saved) {
                throw new RollbackException('Failed to save restored model.');
            }

            return true;
        } catch (\Exception $e) {
            throw new RollbackException("Failed to restore deleted record: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Mark history record as rolled back.
     * 
     * @param ApexHistory $history
     */
    protected function markAsRolledBack(ApexHistory $history): void
    {
        $history->update([
            'rolled_back_at' => now(),
            'rolled_back_by' => Auth::check() ? Auth::id() : null,
        ]);
    }

    /**
     * Log the rollback action itself.
     * 
     * @param ApexHistory $history
     */
    protected function logRollbackAction(ApexHistory $history): void
    {
        $this->auditService->logCustomAction([
            'event_type' => 'rollback_action',
            'action_type' => 'rollback',
            'model_type' => $history->model_type,
            'model_id' => $history->model_id,
            'table_name' => null,
            'additional_data' => [
                'original_history_id' => $history->id,
                'original_action' => $history->action_type,
                'rollback_timestamp' => now()->toISOString(),
                'rollback_user_id' => Auth::check() ? Auth::id() : null,
            ],
            'source_element' => 'rollback-action',
        ]);
    }

    /**
     * Batch rollback multiple history records.
     * 
     * @param array $historyIds Array of history record IDs
     * @return array Results with success/failure for each ID
     */
    public function batchRollback(array $historyIds): array
    {
        $results = [];

        foreach ($historyIds as $historyId) {
            try {
                $success = $this->rollback($historyId);
                $results[$historyId] = [
                    'success' => $success,
                    'error' => null,
                ];
            } catch (\Exception $e) {
                $results[$historyId] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Preview what would happen during a rollback without actually performing it.
     * 
     * @param int $historyId
     * @return array Preview information
     * @throws RollbackException
     */
    public function previewRollback(int $historyId): array
    {
        $history = ApexHistory::findOrFail($historyId);

        // Validate rollback is possible
        $this->validateRollback($history);

        $rollbackData = $history->rollback_data;
        $preview = [
            'history_id' => $historyId,
            'action_type' => $history->action_type,
            'model_type' => $history->model_type,
            'model_id' => $history->model_id,
            'rollback_action' => $rollbackData['action'],
            'description' => $this->getRollbackDescription($history),
        ];

        switch ($rollbackData['action']) {
            case 'restore_values':
                $model = $history->getAuditedModel();
                if ($model) {
                    $currentValues = $model->only(array_keys($rollbackData['values']));
                    $preview['changes'] = [
                        'current_values' => $currentValues,
                        'will_restore_to' => $rollbackData['values'],
                        'affected_fields' => array_keys($rollbackData['values']),
                    ];
                }
                break;

            case 'restore_record':
                $preview['restoration'] = [
                    'will_restore' => true,
                    'restored_values' => $rollbackData['values'],
                    'restoration_method' => $this->getRestorationMethod($history),
                ];
                break;
        }

        return $preview;
    }

    /**
     * Get a human-readable description of what the rollback will do.
     * 
     * @param ApexHistory $history
     * @return string
     */
    protected function getRollbackDescription(ApexHistory $history): string
    {
        $modelName = class_basename($history->model_type);

        switch ($history->action_type) {
            case 'update':
                $fieldCount = count($history->rollback_data['values'] ?? []);
                return "Restore {$fieldCount} field(s) of {$modelName} #{$history->model_id} to previous values";

            case 'delete':
                return "Restore deleted {$modelName} #{$history->model_id}";

            default:
                return "Rollback {$history->action_type} action on {$modelName} #{$history->model_id}";
        }
    }

    /**
     * Determine the restoration method for deleted records.
     * 
     * @param ApexHistory $history
     * @return string
     */
    protected function getRestorationMethod(ApexHistory $history): string
    {
        $modelClass = $history->model_type;

        if (!class_exists($modelClass)) {
            return 'unknown';
        }

        // Check if soft deleted record exists
        if (method_exists($modelClass, 'withTrashed')) {
            $trashedModel = $modelClass::withTrashed()->find($history->model_id);
            if ($trashedModel && $trashedModel->trashed()) {
                return 'soft_delete_restore';
            }
        }

        return 'hard_restore';
    }

    /**
     * Get rollback statistics.
     * 
     * @param string|null $modelType Filter by model type
     * @param int|null $days Number of days to look back
     * @return array
     */
    public function getRollbackStatistics(?string $modelType = null, ?int $days = null): array
    {
        $query = ApexHistory::query();

        if ($modelType) {
            $query->where('model_type', $modelType);
        }

        if ($days) {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        $total = $query->count();
        $rollbackable = $query->where('can_rollback', true)->count();
        $rolledBack = $query->whereNotNull('rolled_back_at')->count();

        return [
            'total_actions' => $total,
            'rollbackable_actions' => $rollbackable,
            'rolled_back_actions' => $rolledBack,
            'rollback_rate' => $rollbackable > 0 ? round(($rolledBack / $rollbackable) * 100, 2) : 0,
            'by_action_type' => $query->select('action_type', DB::raw('count(*) as count'))
                ->groupBy('action_type')
                ->pluck('count', 'action_type')
                ->toArray(),
        ];
    }
}
