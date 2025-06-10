<?php

namespace App\Apex\Widgets;

use App\Apex\Core\Widget\BaseWidget;

class InputNumberWidget extends BaseWidget
{
    public function getType(): string
    {
        return 'inputnumber';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'Unique identifier for the inputnumber widget'
                ],
                'label' => [
                    'type' => 'string',
                    'description' => 'Label to display above the input'
                ],
                'value' => [
                    'type' => 'number',
                    'description' => 'Current value of the input',
                    'default' => 0
                ],
                'min' => [
                    'type' => 'number',
                    'description' => 'Minimum allowed value'
                ],
                'max' => [
                    'type' => 'number',
                    'description' => 'Maximum allowed value'
                ],
                'step' => [
                    'type' => 'number',
                    'description' => 'Step increment/decrement value',
                    'default' => 1
                ],
                'format' => [
                    'type' => 'boolean',
                    'description' => 'Whether to format the value using number formatting',
                    'default' => false
                ],
                'showButtons' => [
                    'type' => 'boolean',
                    'description' => 'Whether to show the increment/decrement buttons',
                    'default' => false
                ],
                'buttonLayout' => [
                    'type' => 'string',
                    'description' => 'Layout of the buttons (stacked, horizontal, vertical)',
                    'enum' => ['stacked', 'horizontal', 'vertical'],
                    'default' => 'stacked'
                ],
                'incrementButtonIcon' => [
                    'type' => 'string',
                    'description' => 'Icon for the increment button',
                    'default' => 'pi pi-plus'
                ],
                'decrementButtonIcon' => [
                    'type' => 'string',
                    'description' => 'Icon for the decrement button',
                    'default' => 'pi pi-minus'
                ],
                'prefix' => [
                    'type' => 'string',
                    'description' => 'Prefix for the input value'
                ],
                'suffix' => [
                    'type' => 'string',
                    'description' => 'Suffix for the input value'
                ],
                'currency' => [
                    'type' => 'string',
                    'description' => 'Currency code for currency mode (e.g. USD)'
                ],
                'currencyDisplay' => [
                    'type' => 'string',
                    'description' => 'Currency display type (symbol, code, name)',
                    'enum' => ['symbol', 'code', 'name']
                ],
                'locale' => [
                    'type' => 'string',
                    'description' => 'Locale for number formatting (e.g. en-US)'
                ],
                'placeholder' => [
                    'type' => 'string',
                    'description' => 'Placeholder text to display when empty'
                ],
                'disabled' => [
                    'type' => 'boolean',
                    'description' => 'Whether the input is disabled',
                    'default' => false
                ],
                'readonly' => [
                    'type' => 'boolean',
                    'description' => 'Whether the input is readonly',
                    'default' => false
                ],
                'size' => [
                    'type' => 'string',
                    'description' => 'Size of the input field',
                    'enum' => ['small', 'medium', 'large'],
                    'default' => 'medium'
                ],
                'helpText' => [
                    'type' => 'string',
                    'description' => 'Help text to display below the input'
                ],
                'mode' => [
                    'type' => 'string',
                    'description' => 'Mode of the inputnumber (decimal, currency, percentage)',
                    'enum' => ['decimal', 'currency', 'percentage'],
                    'default' => 'decimal'
                ],
                'minFractionDigits' => [
                    'type' => 'integer',
                    'description' => 'Minimum fraction digits to display'
                ],
                'maxFractionDigits' => [
                    'type' => 'integer',
                    'description' => 'Maximum fraction digits to display'
                ]
            ],
            'required' => ['id']
        ];
    }

    public function transform(array $config): array
    {
        // Extract properties from config and apply defaults
        return parent::transform($config);
    }
}
