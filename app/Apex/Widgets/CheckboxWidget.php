<?php

namespace App\Apex\Widgets;

use App\Apex\Core\Widget\BaseWidget;

class CheckboxWidget extends BaseWidget
{
    /**
     * Get the widget type identifier
     */
    public function getType(): string
    {
        return 'checkbox';
    }

    /**
     * Get the widget configuration schema
     */
    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'Unique identifier for the checkbox widget'
                ],
                'label' => [
                    'type' => 'string',
                    'description' => 'Label text for the checkbox'
                ],
                'checked' => [
                    'type' => 'boolean',
                    'description' => 'Whether the checkbox is checked by default',
                    'default' => false
                ],
                'disabled' => [
                    'type' => 'boolean',
                    'description' => 'Whether the checkbox is disabled',
                    'default' => false
                ],
                'binary' => [
                    'type' => 'boolean',
                    'description' => 'When enabled, true is returned for checked state and false for unchecked state',
                    'default' => true
                ],
                'invalid' => [
                    'type' => 'boolean',
                    'description' => 'When present, it specifies that the component should have invalid state style',
                    'default' => false
                ],
                'indeterminate' => [
                    'type' => 'boolean',
                    'description' => 'When enabled, it specifies that the component should be in indeterminate state',
                    'default' => false
                ],
                'variant' => [
                    'type' => 'string',
                    'enum' => ['filled', 'outlined'],
                    'description' => 'Specifies the variant style of the checkbox',
                    'default' => 'outlined'
                ],
                'size' => [
                    'type' => 'string',
                    'enum' => ['small', 'medium', 'large'],
                    'description' => 'Size of the checkbox',
                    'default' => 'medium'
                ],
                'name' => [
                    'type' => 'string',
                    'description' => 'Name attribute for the checkbox input'
                ],
                'value' => [
                    'type' => ['string', 'boolean', 'array'],
                    'description' => 'Value of the checkbox'
                ],
                'trueValue' => [
                    'type' => ['string', 'boolean', 'number'],
                    'description' => 'Value returned when the checkbox is checked',
                    'default' => true
                ],
                'falseValue' => [
                    'type' => ['string', 'boolean', 'number'],
                    'description' => 'Value returned when the checkbox is unchecked',
                    'default' => false
                ]
            ],
            'required' => ['label']
        ];
    }

    /**
     * Transform the widget data for frontend consumption
     */
    public function transform(array $config): array
    {
        $transformed = parent::transform($config);

        // Set defaults if not provided
        $defaults = [
            'checked' => false,
            'disabled' => false,
            'binary' => true,
            'invalid' => false,
            'indeterminate' => false,
            'variant' => 'outlined',
            'size' => 'medium',
            'trueValue' => true,
            'falseValue' => false
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($transformed[$key])) {
                $transformed[$key] = $value;
            }
        }

        // Ensure label is set
        if (!isset($transformed['label'])) {
            $transformed['label'] = 'Checkbox';
        }

        return $transformed;
    }
}
