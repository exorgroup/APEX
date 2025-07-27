<?php
// app/Apex/Pro/Widgets/DataTable/Traits/HasConditionalStyle.php

namespace App\Apex\Pro\Widgets\DataTable\Traits;

trait HasConditionalStyle
{
    public function getConditionalStyleSchema(): array
    {
        return [
            'properties' => [
                'conditionalStyles' => [
                    'type' => 'array',
                    'description' => 'Conditional row styling rules',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'column' => [
                                'type' => 'string',
                                'required' => true,
                                'description' => 'Column field name to evaluate'
                            ],
                            'value' => [
                                'type' => ['string', 'number', 'boolean', 'array'],
                                'required' => true,
                                'description' => 'Value to compare against'
                            ],
                            'operator' => [
                                'type' => 'string',
                                'enum' => ['eq', 'ne', 'lt', 'lte', 'gt', 'gte', 'contains', 'startsWith', 'endsWith', 'in', 'notIn'],
                                'default' => 'eq',
                                'description' => 'Comparison operator'
                            ],
                            'priority' => [
                                'type' => 'integer',
                                'default' => 9999,
                                'description' => 'Priority level (1 = highest priority, 9999 = default)'
                            ],
                            'cssClasses' => [
                                'type' => 'string',
                                'description' => 'CSS classes to apply when condition matches'
                            ],
                            'inlineStyles' => [
                                'type' => 'string',
                                'description' => 'Inline CSS styles to apply when condition matches'
                            ],
                            'styleObject' => [
                                'type' => 'object',
                                'description' => 'Style object to apply when condition matches'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function transformConditionalStyle(array $config): array
    {
        return [
            'conditionalStyles' => $config['conditionalStyles'] ?? []
        ];
    }
}
