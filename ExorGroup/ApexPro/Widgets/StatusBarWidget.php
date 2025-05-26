<?php

namespace ExorGroup\ApexPro\Widgets;

class StatusBarWidget extends BaseProWidget
{
    protected function renderWidget(array $params): string
    {
        // Calculate percentages and prepare data
        $total = $this->calculateTotal($params['values']);
        $processedValues = $this->processValues($params['values'], $total);

        return $this->view('widgets.status-bar', array_merge($params, [
            'processedValues' => $processedValues,
            'total' => $total,
            'formattedTotal' => $this->formatValue($total, $params['unit'])
        ]));
    }

    public function getDefaults(): array
    {
        return [
            'title' => 'Status Overview',
            'titleClass' => 'status-bar-title',
            'height' => '8px',
            'gap' => '2px',
            'unit' => '',
            'showLegend' => true,
            'legendPosition' => 'bottom', // 'top', 'bottom', 'left', 'right'
            'borderRadius' => '12px',
            'backgroundColor' => '#f3f4f6',
            'containerClass' => '',
            'legendClass' => '',
            'shadow' => true,
            'borderWidth' => '1px',
            'borderColor' => '#e5e7eb',
            'padding' => '1.5rem',
            'values' => [
                [
                    'label' => 'Used',
                    'value' => 50,
                    'color' => '#3b82f6',
                    'textClass' => ''
                ],
                [
                    'label' => 'Free',
                    'value' => 50,
                    'color' => '#e5e7eb',
                    'textClass' => ''
                ]
            ]
        ];
    }

    /**
     * Calculate total value from all segments
     */
    private function calculateTotal(array $values): float
    {
        return array_sum(array_column($values, 'value'));
    }

    /**
     * Process values to include percentages and validation
     */
    private function processValues(array $values, float $total): array
    {
        $processed = [];

        foreach ($values as $value) {
            if (!isset($value['value']) || !isset($value['label'])) {
                continue; // Skip invalid entries
            }

            $percentage = $total > 0 ? ($value['value'] / $total) * 100 : 0;

            $processed[] = [
                'label' => $value['label'],
                'value' => $value['value'],
                'percentage' => round($percentage, 2),
                'color' => $value['color'] ?? '#e5e7eb',
                'textClass' => $value['textClass'] ?? ''
            ];
        }

        return $processed;
    }

    /**
     * Format value with unit - simplified version
     */
    private function formatValue(float $value, string $unit): string
    {
        if (empty($unit)) {
            return number_format($value, 2);
        }

        return number_format($value, 2) . ' ' . $unit;
    }

    /**
     * Validate widget parameters
     */
    public function validateParams(array $params): array
    {
        $errors = parent::validateParams($params);

        // Validate values array
        if (empty($params['values']) || !is_array($params['values'])) {
            $errors[] = 'Values parameter must be a non-empty array';
        } else {
            foreach ($params['values'] as $index => $value) {
                if (!is_array($value)) {
                    $errors[] = "Value at index {$index} must be an array";
                    continue;
                }

                if (!isset($value['label']) || empty($value['label'])) {
                    $errors[] = "Value at index {$index} must have a non-empty label";
                }

                if (!isset($value['value']) || !is_numeric($value['value'])) {
                    $errors[] = "Value at index {$index} must have a numeric value";
                }

                if (isset($value['color']) && !preg_match('/^#[0-9A-Fa-f]{6}$/', $value['color']) && !preg_match('/^#[0-9A-Fa-f]{3}$/', $value['color'])) {
                    // Allow CSS color names and other formats, just warn about hex
                    if (!in_array(strtolower($value['color']), ['red', 'blue', 'green', 'yellow', 'orange', 'purple', 'pink', 'gray', 'black', 'white'])) {
                        // Only validate if it looks like it should be hex
                        if (strpos($value['color'], '#') === 0) {
                            $errors[] = "Color at index {$index} should be a valid hex color (e.g., #ff0000)";
                        }
                    }
                }
            }
        }

        // Validate height
        if (!preg_match('/^\d+px$/', $params['height'])) {
            $errors[] = 'Height must be in px format (e.g., "8px")';
        }

        // Validate gap
        if (!preg_match('/^\d+px$/', $params['gap'])) {
            $errors[] = 'Gap must be in px format (e.g., "2px")';
        }

        // Validate legend position
        if (!in_array($params['legendPosition'], ['top', 'bottom', 'left', 'right'])) {
            $errors[] = 'Legend position must be one of: top, bottom, left, right';
        }

        return $errors;
    }
}
