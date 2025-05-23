<?php

namespace App\Apex\Widgets;

/**
 * Test Widget
 * 
 * A simple test widget for validating APEX framework functionality.
 * 
 * Usage: !!apex-testWidget:{"title":"My Title", "content":"My Content"}!!
 */
class TestWidget extends BaseWidget
{
    /**
     * Get default parameters for the widget
     */
    public function getDefaults(): array
    {
        return array_merge(parent::getDefaults(), [
            'title' => 'Test Widget',
            'content' => 'Default test content',
            'showDebug' => false,
        ]);
    }

    /**
     * Validate widget parameters
     */
    public function validateParams(array $params): array
    {
        $errors = parent::validateParams($params);

        // Validate title
        if (empty($params['title'])) {
            $errors[] = 'Title parameter cannot be empty';
        }

        // Validate content
        if (empty($params['content'])) {
            $errors[] = 'Content parameter cannot be empty';
        }

        return $errors;
    }

    /**
     * Render the test widget
     */
    protected function renderWidget(array $params): string
    {
        // Add debug information to params if enabled
        if ($params['showDebug']) {
            $params['debugInfo'] = "<!-- APEX Test Widget Debug: ID={$params['id']}, Title={$params['title']} -->";
        }

        // Render using the dedicated view template
        return $this->view('test-widget', $params);
    }
}
