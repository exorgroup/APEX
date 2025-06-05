<?php

namespace App\Apex\Widgets;

use App\Apex\Core\Widget\BaseWidget;

class KnobWidget extends BaseWidget
{
    public function getType(): string
    {
        return 'knob';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'Unique identifier for the knob widget'
                ],
                'value' => [
                    'type' => 'number',
                    'description' => 'Current value of the knob',
                    'default' => 0
                ],
                'min' => [
                    'type' => 'number',
                    'description' => 'Minimum value',
                    'default' => 0
                ],
                'max' => [
                    'type' => 'number',
                    'description' => 'Maximum value',
                    'default' => 100
                ],
                'step' => [
                    'type' => 'number',
                    'description' => 'Step increment',
                    'default' => 1
                ],
                'size' => [
                    'type' => 'number',
                    'description' => 'Size of the knob in pixels',
                    'default' => 100
                ],
                'strokeWidth' => [
                    'type' => 'number',
                    'description' => 'Width of the knob stroke',
                    'default' => 6
                ],
                'showValue' => [
                    'type' => 'boolean',
                    'description' => 'Whether to show the value',
                    'default' => true
                ],
                'valueTemplate' => [
                    'type' => 'string',
                    'description' => 'Template for displaying the value',
                    'default' => '{value}'
                ],
                'disabled' => [
                    'type' => 'boolean',
                    'description' => 'Whether the knob is disabled',
                    'default' => false
                ],
                'readonly' => [
                    'type' => 'boolean',
                    'description' => 'Whether the knob is readonly',
                    'default' => false
                ],
                'valueColor' => [
                    'type' => 'string',
                    'description' => 'Color of the value text',
                    'default' => null
                ],
                'rangeColor' => [
                    'type' => 'string',
                    'description' => 'Color of the range',
                    'default' => null
                ],
                'textColor' => [
                    'type' => 'string',
                    'description' => 'Color of the text',
                    'default' => null
                ]
            ]
        ];
    }

    public function transform(array $config): array
    {
        $transformed = parent::transform($config);

        // Ensure numeric values are properly typed
        $numericFields = ['value', 'min', 'max', 'step', 'size', 'strokeWidth'];
        foreach ($numericFields as $field) {
            if (isset($transformed[$field])) {
                $transformed[$field] = (float) $transformed[$field];
            }
        }

        // Ensure boolean values are properly typed
        $booleanFields = ['showValue', 'disabled', 'readonly'];
        foreach ($booleanFields as $field) {
            if (isset($transformed[$field])) {
                $transformed[$field] = (bool) $transformed[$field];
            }
        }

        return $transformed;
    }
}
