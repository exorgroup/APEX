<?php

namespace App\Apex\Widgets;

/**
 * Logo Widget
 * 
 * Displays a logo image with optional link functionality.
 * 
 * Usage: !!apex-logo:{"src":"/images/logo.svg", "alt":"Company Logo", "route":"dashboard"}!!
 */
class LogoWidget extends BaseWidget
{
    /**
     * Get default parameters for the widget
     */
    public function getDefaults(): array
    {
        return array_merge(parent::getDefaults(), [
            'src' => '/images/logo.svg',
            'alt' => config('app.name', 'Application') . ' Logo',
            'width' => '150px',
            'height' => 'auto',
            'route' => '',
            'url' => '',
            'target' => '_self',
            'title' => '',
        ]);
    }

    /**
     * Validate widget parameters
     */
    public function validateParams(array $params): array
    {
        $errors = parent::validateParams($params);

        // Validate src parameter
        if (empty($params['src'])) {
            $errors[] = 'Logo src parameter is required';
        }

        // Validate dimensions
        if (!empty($params['width']) && !$this->isValidCssUnit($params['width'])) {
            $errors[] = 'Invalid width format. Use CSS units like "150px", "10rem", or "100%"';
        }

        if (!empty($params['height']) && !$this->isValidCssUnit($params['height'])) {
            $errors[] = 'Invalid height format. Use CSS units like "150px", "10rem", or "100%"';
        }

        // Validate target
        if (!in_array($params['target'], ['_self', '_blank', '_parent', '_top'])) {
            $errors[] = 'Invalid target value. Use "_self", "_blank", "_parent", or "_top"';
        }

        return $errors;
    }

    /**
     * Render the logo widget
     */
    protected function renderWidget(array $params): string
    {
        $url = $this->resolveUrl($params);
        $hasLink = $url !== '#';

        // Build image attributes
        $imgAttributes = [
            'src' => $params['src'],
            'alt' => $params['alt'],
            'class' => 'apex-logo-image',
        ];

        if (!empty($params['width'])) {
            $imgAttributes['style'] = ($imgAttributes['style'] ?? '') . "width: {$params['width']};";
        }

        if (!empty($params['height'])) {
            $imgAttributes['style'] = ($imgAttributes['style'] ?? '') . "height: {$params['height']};";
        }

        if (!empty($params['title'])) {
            $imgAttributes['title'] = $params['title'];
        }

        // Build image HTML
        $imgHtml = '<img ' . $this->buildAttributes($imgAttributes) . ' />';

        // Wrap in link if URL is provided
        if ($hasLink) {
            $linkAttributes = [
                'href' => $url,
                'class' => 'apex-logo-link ' . $params['cssClass'],
                'id' => $params['id'],
                'target' => $params['target'],
            ];

            if (!empty($params['title'])) {
                $linkAttributes['title'] = $params['title'];
            }

            return '<a ' . $this->buildAttributes($linkAttributes) . '>' . $imgHtml . '</a>';
        }

        // Return just the image with container
        $containerAttributes = [
            'class' => 'apex-logo ' . $params['cssClass'],
            'id' => $params['id'],
        ];

        return '<div ' . $this->buildAttributes($containerAttributes) . '>' . $imgHtml . '</div>';
    }

    /**
     * Check if a value is a valid CSS unit
     */
    protected function isValidCssUnit(string $value): bool
    {
        // Allow common CSS units
        return preg_match('/^\d+(\.\d+)?(px|em|rem|%|vh|vw|ex|ch|cm|mm|in|pt|pc)$/', $value) ||
            in_array($value, ['auto', 'inherit', 'initial']);
    }

    /**
     * Build HTML attributes string
     */
    protected function buildAttributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            if ($value !== null && $value !== '') {
                $html[] = $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }

        return implode(' ', $html);
    }
}
