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
        // Debug information (if enabled)
        $debugInfo = '';
        if ($params['showDebug']) {
            $debugInfo = '<!-- APEX Test Widget Debug: ID=' . $params['id'] . ', Title=' . $params['title'] . ' -->';
        }

        // Build the widget HTML
        $html = $debugInfo . PHP_EOL;
        $html .= '<div class="apex-test-widget ' . $params['cssClass'] . '" id="' . htmlspecialchars($params['id']) . '">' . PHP_EOL;
        $html .= '    <h3 class="text-xl font-bold mb-2">' . htmlspecialchars($params['title']) . '</h3>' . PHP_EOL;
        $html .= '    <div class="content">' . PHP_EOL;
        //  $html .= '        ' . htmlspecialchars($params['content']) . PHP_EOL;
        $html .= '        ' . $params['content'] . PHP_EOL; // No htmlspecialchars if you want raw HTML
        $html .= '    </div>' . PHP_EOL;
        $html .= '</div>';

        return $html;
    }
}
