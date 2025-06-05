<?php

namespace App\Apex\Widgets;

use App\Apex\Core\Widget\BaseWidget;

class DatePickerWidget extends BaseWidget
{
    public function getType(): string
    {
        return 'datepicker';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'Unique identifier for the datepicker widget'
                ],
                'value' => [
                    'type' => ['string', 'null'],
                    'description' => 'Selected date value (ISO string format)',
                    'default' => null
                ],
                'placeholder' => [
                    'type' => 'string',
                    'description' => 'Placeholder text',
                    'default' => 'Select date'
                ],
                'dateFormat' => [
                    'type' => 'string',
                    'description' => 'Date format pattern',
                    'default' => 'mm/dd/yy'
                ],
                'inline' => [
                    'type' => 'boolean',
                    'description' => 'Display as inline calendar',
                    'default' => false
                ],
                'showIcon' => [
                    'type' => 'boolean',
                    'description' => 'Show calendar icon',
                    'default' => true
                ],
                'showButtonBar' => [
                    'type' => 'boolean',
                    'description' => 'Show today/clear buttons',
                    'default' => false
                ],
                'showTime' => [
                    'type' => 'boolean',
                    'description' => 'Include time picker',
                    'default' => false
                ],
                'timeOnly' => [
                    'type' => 'boolean',
                    'description' => 'Show only time picker',
                    'default' => false
                ],
                'hourFormat' => [
                    'type' => 'string',
                    'description' => 'Hour format (12 or 24)',
                    'default' => '24'
                ],
                'disabled' => [
                    'type' => 'boolean',
                    'description' => 'Disable the datepicker',
                    'default' => false
                ],
                'readonly' => [
                    'type' => 'boolean',
                    'description' => 'Make readonly',
                    'default' => false
                ],
                'minDate' => [
                    'type' => ['string', 'null'],
                    'description' => 'Minimum selectable date (ISO string)',
                    'default' => null
                ],
                'maxDate' => [
                    'type' => ['string', 'null'],
                    'description' => 'Maximum selectable date (ISO string)',
                    'default' => null
                ],
                'disabledDates' => [
                    'type' => 'array',
                    'description' => 'Array of disabled dates',
                    'items' => [
                        'type' => 'string'
                    ],
                    'default' => []
                ],
                'selectionMode' => [
                    'type' => 'string',
                    'description' => 'Selection mode: single, multiple, range',
                    'default' => 'single'
                ],
                'numberOfMonths' => [
                    'type' => 'number',
                    'description' => 'Number of months to display',
                    'default' => 1
                ],
                'view' => [
                    'type' => 'string',
                    'description' => 'Initial view: date, month, year',
                    'default' => 'date'
                ],
                'touchUI' => [
                    'type' => 'boolean',
                    'description' => 'Enable touch UI mode',
                    'default' => false
                ]
            ]
        ];
    }

    public function transform(array $config): array
    {
        $transformed = parent::transform($config);

        // Convert date strings to Date objects will be handled in Vue component
        // Just ensure proper types here
        $booleanFields = ['inline', 'showIcon', 'showButtonBar', 'showTime', 'timeOnly', 'disabled', 'readonly', 'touchUI'];
        foreach ($booleanFields as $field) {
            if (isset($transformed[$field])) {
                $transformed[$field] = (bool) $transformed[$field];
            }
        }

        if (isset($transformed['numberOfMonths'])) {
            $transformed['numberOfMonths'] = (int) $transformed['numberOfMonths'];
        }

        return $transformed;
    }
}
