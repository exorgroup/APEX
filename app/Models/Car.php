<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Apex\Audit\Traits\ApexAuditable;

class Car extends Model
{
    use HasFactory, SoftDeletes, ApexAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'make',
        'model',
        'year',
        'color',
        'vin',
        'price',
        'mileage',
        'status',
        'owner_name',
        'purchase_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year' => 'integer',
        'price' => 'decimal:2',
        'mileage' => 'integer',
        'purchase_date' => 'date',
    ];

    /**
     * APEX Audit configuration for this model.
     *
     * @var array
     */
    protected $apexAuditConfig = [
        // Which events to audit (default: all)
        'audit_events' => ['create', 'update', 'delete', 'restore'],

        // Fields to exclude from audit (these won't be tracked)
        'audit_exclude' => ['notes'], // Don't track notes field

        // Fields to exclude from history display (tracked but not shown)
        'history_exclude' => ['vin'], // Track VIN but don't show in history UI

        // Actions that can be rolled back
        'rollbackable_actions' => ['update', 'delete'],

        // Field-specific rules
        'audit_rules' => [
            'price' => [
                'track_changes_only' => true, // Only log if value changes
                'minimum_change' => 100, // Only track price changes > $100
            ],
            'mileage' => [
                'minimum_change' => 1000, // Only track mileage changes > 1000
            ],
        ],
    ];

    /**
     * Get a human-readable description for audit logs.
     *
     * @param string $action
     * @param array $values
     * @return string
     */
    public function getAuditDescription(string $action, array $values = []): string
    {
        $identifier = "{$this->year} {$this->make} {$this->model}";

        switch ($action) {
            case 'create':
                return "Added new car: {$identifier}";
            case 'update':
                $changes = array_keys($values);
                $changeList = implode(', ', $changes);
                return "Updated {$identifier} - Changed: {$changeList}";
            case 'delete':
                return "Deleted car: {$identifier}";
            case 'restore':
                return "Restored car: {$identifier}";
            default:
                return "Performed {$action} on {$identifier}";
        }
    }

    /**
     * Get the connection name for the model.
     * This ensures the model uses the tenant connection when in tenant context.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        // If we're in a tenant context, use the tenant connection
        if (function_exists('tenant') && tenant()) {
            return 'tenant';
        }

        // Otherwise use default
        return parent::getConnectionName();
    }

    /**
     * Get custom audit data for this model.
     *
     * @return array
     */
    public function getCustomAuditData(): array
    {
        return [
            'car_value' => $this->price,
            'car_age' => now()->year - $this->year,
            'is_vintage' => $this->year < 1990,
        ];
    }

    /**
     * Get field labels for history display.
     *
     * @param string $field
     * @return string
     */
    public function getFieldLabel(string $field): string
    {
        $labels = [
            'make' => 'Manufacturer',
            'model' => 'Model Name',
            'year' => 'Year',
            'color' => 'Color',
            'vin' => 'VIN Number',
            'price' => 'Price ($)',
            'mileage' => 'Mileage',
            'status' => 'Status',
            'owner_name' => 'Owner Name',
            'purchase_date' => 'Purchase Date',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
