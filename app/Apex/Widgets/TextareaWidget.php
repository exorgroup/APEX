<?php

namespace App\Apex\Widgets;

use App\Apex\Core\Widget\BaseWidget;

class TextareaWidget extends BaseWidget
{
    public function getType(): string
    {
        return 'textarea';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'Unique identifier for the textarea widget'
                ],
                'value' => [
                    'type' => ['string', 'null'],
                    'description' => 'Textarea content value',
                    'default' => ''
                ],
                'label' => [
                    'type' => 'string',
                    'description' => 'Label text for the textarea',
                    'default' => ''
                ],
                'placeholder' => [
                    'type' => 'string',
                    'description' => 'Placeholder text',
                    'default' => ''
                ],
                'rows' => [
                    'type' => 'integer',
                    'description' => 'Number of rows to display',
                    'default' => 5
                ],
                'cols' => [
                    'type' => ['integer', 'null'],
                    'description' => 'Number of columns to display',
                    'default' => null
                ],
                'autoResize' => [
                    'type' => 'boolean',
                    'description' => 'Whether the textarea should auto-resize',
                    'default' => false
                ],
                'disabled' => [
                    'type' => 'boolean',
                    'description' => 'Whether the textarea is disabled',
                    'default' => false
                ],
                'readonly' => [
                    'type' => 'boolean',
                    'description' => 'Whether the textarea is read-only',
                    'default' => false
                ],
                'required' => [
                    'type' => 'boolean',
                    'description' => 'Whether the textarea is required',
                    'default' => false
                ],
                'maxLength' => [
                    'type' => ['integer', 'null'],
                    'description' => 'Maximum character length',
                    'default' => null
                ],
                'feedback' => [
                    'type' => 'boolean',
                    'description' => 'Whether to show validation feedback',
                    'default' => false
                ],
                'invalidMessage' => [
                    'type' => 'string',
                    'description' => 'Message to display when validation fails',
                    'default' => ''
                ],
                'helpText' => [
                    'type' => 'string',
                    'description' => 'Help text to display below the textarea',
                    'default' => ''
                ]
            ],
            'required' => ['id']
        ];
    }

    public function transform(array $config): array
    {
        // Any custom transformations can be done here

        // Call parent transform for standard processing
        return parent::transform($config);
    }
}
