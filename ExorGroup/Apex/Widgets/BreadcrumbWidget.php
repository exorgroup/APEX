<?php

namespace ExorGroup\Apex\Widgets;

/**
 * Breadcrumb Widget
 * 
 * A customizable breadcrumb navigation widget with 5 different styles.
 * 
 * Usage: !!apex-breadcrumb:{"items":[{"label":"Home","url":"/"},{"label":"Products","url":"/products"},{"label":"Current Page"}]}!!
 */
class BreadcrumbWidget extends BaseWidget
{
    /**
     * Get default parameters for the widget
     */
    public function getDefaults(): array
    {
        return array_merge(parent::getDefaults(), [
            'items' => [],
            'delimiter' => '/',
            'style' => 1, // 1-5 different styles
            'showBorder' => true,
            'showShadow' => false,
            'borderWidth' => '1px',
            'borderColor' => '#e5e7eb',
            'backgroundColor' => '#ffffff',
            'textColor' => '#374151',
            'linkColor' => '#374151',
            'hoverColor' => '#1f2937',
            'activeColor' => '#6b7280',
            'fontSize' => '14px',
            'fontWeight' => '400',
            'padding' => '8px 16px',
            'borderRadius' => '6px',
            'gap' => '8px',
            'height' => 'auto',
        ]);
    }

    /**
     * Validate widget parameters
     */
    public function validateParams(array $params): array
    {
        $errors = parent::validateParams($params);

        // Validate items
        if (!is_array($params['items'])) {
            $errors[] = 'Items parameter must be an array';
        }

        // Validate style
        if (!in_array($params['style'], [1, 2, 3, 4, 5])) {
            $errors[] = 'Style must be between 1 and 5';
        }

        // Validate each item structure
        foreach ($params['items'] as $index => $item) {
            if (!is_array($item) || empty($item['label'])) {
                $errors[] = "Item at index {$index} must have a 'label' property";
            }
        }

        return $errors;
    }

    /**
     * Render the breadcrumb widget
     */
    protected function renderWidget(array $params): string
    {
        return $this->view('widgets.breadcrumb', $params);
    }
}
