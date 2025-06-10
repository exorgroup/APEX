<?php

namespace App\Apex\Widgets;

use App\Apex\Core\Widget\BaseWidget;

class SelectWidget extends BaseWidget
{
    public function getType(): string
    {
        return 'select';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'Unique identifier for the select widget'
                ],
                'value' => [
                    'type' => ['string', 'array', 'null'],
                    'description' => 'Selected value(s)',
                    'default' => null
                ],
                'options' => [
                    'type' => 'array',
                    'description' => 'Array of select options',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'label' => [
                                'type' => 'string',
                                'description' => 'Display label'
                            ],
                            'value' => [
                                'type' => ['string', 'number'],
                                'description' => 'Option value'
                            ],
                            'disabled' => [
                                'type' => 'boolean',
                                'description' => 'Whether option is disabled',
                                'default' => false
                            ],
                            'icon' => [
                                'type' => 'string',
                                'description' => 'Optional icon class',
                                'default' => null
                            ]
                        ],
                        'required' => ['label', 'value']
                    ],
                    'default' => []
                ],
                'optionLabel' => [
                    'type' => 'string',
                    'description' => 'Property name for option label when using object array',
                    'default' => 'label'
                ],
                'optionValue' => [
                    'type' => 'string',
                    'description' => 'Property name for option value when using object array',
                    'default' => 'value'
                ],
                'optionDisabled' => [
                    'type' => 'string',
                    'description' => 'Property name for option disabled state',
                    'default' => 'disabled'
                ],
                'placeholder' => [
                    'type' => 'string',
                    'description' => 'Placeholder text',
                    'default' => 'Select an option'
                ],
                'multiple' => [
                    'type' => 'boolean',
                    'description' => 'Allow multiple selection',
                    'default' => false
                ],
                'disabled' => [
                    'type' => 'boolean',
                    'description' => 'Disable the select',
                    'default' => false
                ],
                'filter' => [
                    'type' => 'boolean',
                    'description' => 'Enable filtering',
                    'default' => false
                ],
                'filterPlaceholder' => [
                    'type' => 'string',
                    'description' => 'Filter input placeholder',
                    'default' => 'Search'
                ],
                'showClear' => [
                    'type' => 'boolean',
                    'description' => 'Show clear button',
                    'default' => false
                ],
                'editable' => [
                    'type' => 'boolean',
                    'description' => 'Allow custom text input',
                    'default' => false
                ],
                'checkmark' => [
                    'type' => 'boolean',
                    'description' => 'Show checkmark for selected items',
                    'default' => false
                ],
                'highlightOnSelect' => [
                    'type' => 'boolean',
                    'description' => 'Highlight selected items',
                    'default' => true
                ],
                'display' => [
                    'type' => 'string',
                    'description' => 'Display mode for multiple selection',
                    'enum' => ['comma', 'chip'],
                    'default' => 'comma'
                ],
                'required' => [
                    'type' => 'boolean',
                    'description' => 'Mark as required field',
                    'default' => false
                ],
                'invalid' => [
                    'type' => 'boolean',
                    'description' => 'Mark as invalid',
                    'default' => false
                ],
                'label' => [
                    'type' => 'string',
                    'description' => 'Field label',
                    'default' => null
                ],
                'helpText' => [
                    'type' => 'string',
                    'description' => 'Help text displayed below the select',
                    'default' => null
                ],
                'loading' => [
                    'type' => 'boolean',
                    'description' => 'Show loading state',
                    'default' => false
                ],
                'loadingIcon' => [
                    'type' => 'string',
                    'description' => 'Loading icon class',
                    'default' => 'pi pi-spinner'
                ],
                'variant' => [
                    'type' => 'string',
                    'description' => 'Select variant',
                    'enum' => ['filled', 'outlined'],
                    'default' => 'outlined'
                ],
                'size' => [
                    'type' => 'string',
                    'description' => 'Size of the select',
                    'enum' => ['small', 'normal', 'large'],
                    'default' => 'normal'
                ]
            ],
            'required' => ['id', 'options']
        ];
    }

    public function render(array $props = []): array
    {
        // Generate unique ID if not provided
        if (!isset($props['id'])) {
            $props['id'] = 'select-' . uniqid();
        }

        // Ensure options array exists
        if (!isset($props['options'])) {
            $props['options'] = [];
        }

        return [
            'id' => $props['id'],
            'type' => $this->getType(),
            'props' => $props,
        ];
    }

    public function validate(array $props): bool
    {
        // Basic validation
        if (!isset($props['options']) || !is_array($props['options'])) {
            return false;
        }

        // Validate each option has required fields
        foreach ($props['options'] as $option) {
            if (!is_array($option) || !isset($option['label']) || !isset($option['value'])) {
                return false;
            }
        }

        return true;
    }
}
