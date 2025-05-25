<?php

namespace ExorGroup\Apex\Widgets;

/**
 * Rating Widget
 * 
 * A rating indicator widget that displays various shapes with partial fill support.
 * 
 * Usage: {{apex-rating:{"total":5, "filled":3.5, "type":"star", "borderColor":"#ccc", "filledColor":"#ffd700"}}}
 */
class RatingWidget extends BaseWidget
{
    /**
     * Get default parameters for the widget
     */
    public function getDefaults(): array
    {
        return array_merge(parent::getDefaults(), [
            'total' => 5,
            'filled' => 0,
            'borderColor' => '#e5e7eb',
            'filledColor' => '#fbbf24',
            'borderThickness' => 2,
            'type' => 'star',
            'height' => 24,
            'orientation' => 'horizontal',
        ]);
    }

    /**
     * Validate widget parameters
     */
    public function validateParams(array $params): array
    {
        $errors = parent::validateParams($params);

        // Validate total
        if (!is_numeric($params['total']) || $params['total'] <= 0) {
            $errors[] = 'Total must be a positive number';
        }

        // Validate filled
        if (!is_numeric($params['filled']) || $params['filled'] < 0) {
            $errors[] = 'Filled must be a non-negative number';
        }

        // Validate filled doesn't exceed total
        if ($params['filled'] > $params['total']) {
            $errors[] = 'Filled value cannot exceed total';
        }

        // Validate type
        $allowedTypes = ['star', 'circle', 'square', 'diamond', 'arrow-left', 'arrow-right', 'pill'];
        if (!in_array($params['type'], $allowedTypes)) {
            $errors[] = 'Invalid type. Allowed types: ' . implode(', ', $allowedTypes);
        }

        // Validate orientation
        if (!in_array($params['orientation'], ['horizontal', 'vertical'])) {
            $errors[] = 'Invalid orientation. Must be "horizontal" or "vertical"';
        }

        // Validate border thickness
        if (!is_numeric($params['borderThickness']) || $params['borderThickness'] < 0) {
            $errors[] = 'Border thickness must be a non-negative number';
        }

        // Validate height
        if (!is_numeric($params['height']) || $params['height'] <= 0) {
            $errors[] = 'Height must be a positive number';
        }

        return $errors;
    }

    /**
     * Render the rating widget
     */
    protected function renderWidget(array $params): string
    {
        $svgTemplates = $this->getSvgTemplates();
        $svgTemplate = $svgTemplates[$params['type']];

        $indicators = [];

        for ($i = 1; $i <= $params['total']; $i++) {
            $fillPercentage = $this->calculateFillPercentage($i, $params['filled']);
            $indicators[] = $this->renderIndicator($svgTemplate, $fillPercentage, $params);
        }

        return $this->view('widgets.rating', [
            'indicators' => $indicators,
            'orientation' => $params['orientation'],
            'height' => $params['height'],
            'cssClass' => $params['cssClass'],
            'id' => $params['id']
        ]);
    }

    /**
     * Calculate fill percentage for a specific indicator
     */
    private function calculateFillPercentage(int $position, float $filled): float
    {
        if ($filled >= $position) {
            return 100;
        } elseif ($filled >= $position - 1) {
            return ($filled - ($position - 1)) * 100;
        } else {
            return 0;
        }
    }

    /**
     * Render a single indicator with partial fill
     */
    private function renderIndicator(string $svgTemplate, float $fillPercentage, array $params): string
    {
        $uniqueId = 'gradient-' . uniqid();
        $maskId = 'mask-' . uniqid();
        $isVertical = $params['orientation'] === 'vertical';

        // Create the gradient and mask definitions for partial fill
        $gradientDef = '';
        if ($fillPercentage > 0 && $fillPercentage < 100) {
            $gradientDef = sprintf(
                '<defs>
                    <linearGradient id="%s" x1="0%%" y1="0%%" x2="100%%" y2="0%%">
                        <stop offset="0%%" style="stop-color:%s;stop-opacity:1" />
                        <stop offset="%s%%" style="stop-color:%s;stop-opacity:1" />
                        <stop offset="%s%%" style="stop-color:transparent;stop-opacity:0" />
                        <stop offset="100%%" style="stop-color:transparent;stop-opacity:0" />
                    </linearGradient>
                    <mask id="%s">
                        <rect x="0" y="0" width="%s%%" height="100%%" fill="white"/>
                        <rect x="%s%%" y="0" width="%s%%" height="100%%" fill="black"/>
                    </mask>
                </defs>',
                $uniqueId,
                $params['filledColor'],
                $fillPercentage,
                $params['filledColor'],
                $fillPercentage,
                $maskId,
                $fillPercentage,
                $fillPercentage,
                100 - $fillPercentage
            );
        }

        // For partial fills, we need to render two overlapping SVGs
        if ($fillPercentage > 0 && $fillPercentage < 100) {
            // Create filled and unfilled versions
            $filledSvg = str_replace([
                '{{WIDTH}}',
                '{{HEIGHT}}',
                '{{FILL}}',
                '{{STROKE}}',
                '{{STROKE_WIDTH}}',
                '{{GRADIENT_DEF}}'
            ], [
                $params['height'],
                $params['height'],
                $params['filledColor'],
                $params['borderColor'],
                $params['borderThickness'],
                ''
            ], $svgTemplate);

            $unfilledSvg = str_replace([
                '{{WIDTH}}',
                '{{HEIGHT}}',
                '{{FILL}}',
                '{{STROKE}}',
                '{{STROKE_WIDTH}}',
                '{{GRADIENT_DEF}}'
            ], [
                $params['height'],
                $params['height'],
                'none',
                $params['borderColor'],
                $params['borderThickness'],
                ''
            ], $svgTemplate);

            // For vertical orientation, we rotate the entire container and adjust the fill direction
            if ($isVertical) {
                return sprintf(
                    '<div class="rating-indicator-container vertical-container" style="position: relative; display: inline-block; width: %spx; height: %spx; transform: rotate(90deg);">
                        <div class="rating-filled" style="position: absolute; top: 0; left: 0; width: %s%%; height: 100%%; overflow: hidden;">%s</div>
                        <div class="rating-unfilled" style="position: absolute; top: 0; left: 0; width: 100%%; height: 100%%;">%s</div>
                    </div>',
                    $params['height'],
                    $params['height'],
                    $fillPercentage,
                    $filledSvg,
                    $unfilledSvg
                );
            } else {
                // Horizontal orientation (original code)
                return sprintf(
                    '<div class="rating-indicator-container" style="position: relative; display: inline-block; width: %spx; height: %spx;">
                        <div class="rating-filled" style="position: absolute; top: 0; left: 0; width: %s%%; height: 100%%; overflow: hidden;">%s</div>
                        <div class="rating-unfilled" style="position: absolute; top: 0; left: 0; width: 100%%; height: 100%%;">%s</div>
                    </div>',
                    $params['height'],
                    $params['height'],
                    $fillPercentage,
                    $filledSvg,
                    $unfilledSvg
                );
            }
        }

        // For fully filled or empty indicators, use simple approach
        $fill = 'none';
        if ($fillPercentage >= 100) {
            $fill = $params['filledColor'];
        }

        // Replace template variables
        $svg = str_replace([
            '{{WIDTH}}',
            '{{HEIGHT}}',
            '{{FILL}}',
            '{{STROKE}}',
            '{{STROKE_WIDTH}}',
            '{{GRADIENT_DEF}}'
        ], [
            $params['height'],
            $params['height'],
            $fill,
            $params['borderColor'],
            $params['borderThickness'],
            $gradientDef
        ], $svgTemplate);

        // Apply rotation for vertical orientation to fully filled/empty indicators
        if ($isVertical) {
            $svg = '<div style="transform: rotate(90deg); display: inline-block; transform-origin: center center;">' . $svg . '</div>';
        }

        return $svg;
    }

    /**
     * Get SVG templates for different indicator types
     */
    private function getSvgTemplates(): array
    {
        return [
            'star' => '{{GRADIENT_DEF}}<svg xmlns="http://www.w3.org/2000/svg" width="{{WIDTH}}" height="{{HEIGHT}}" viewBox="0 0 24 24" fill="{{FILL}}" stroke="{{STROKE}}" stroke-width="{{STROKE_WIDTH}}" stroke-linecap="round" stroke-linejoin="round"> <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>',

            'circle' => '{{GRADIENT_DEF}}<svg xmlns="http://www.w3.org/2000/svg" width="{{WIDTH}}" height="{{HEIGHT}}" viewBox="0 0 24 24" fill="{{FILL}}" stroke="{{STROKE}}" stroke-width="{{STROKE_WIDTH}}" stroke-linecap="round" stroke-linejoin="round"> <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /></svg>',

            'square' => '{{GRADIENT_DEF}}<svg xmlns="http://www.w3.org/2000/svg" width="{{WIDTH}}" height="{{HEIGHT}}" viewBox="0 0 24 24" fill="{{FILL}}" stroke="{{STROKE}}" stroke-width="{{STROKE_WIDTH}}" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /></svg>',

            'diamond' => '{{GRADIENT_DEF}}<svg xmlns="http://www.w3.org/2000/svg" width="{{WIDTH}}" height="{{HEIGHT}}" viewBox="0 0 24 24" fill="{{FILL}}" stroke="{{STROKE}}" stroke-width="{{STROKE_WIDTH}}" stroke-linecap="round" stroke-linejoin="round"> <path d="M13.446 2.6l7.955 7.954a2.045 2.045 0 0 1 0 2.892l-7.955 7.955a2.045 2.045 0 0 1 -2.892 0l-7.955 -7.955a2.045 2.045 0 0 1 0 -2.892l7.955 -7.955a2.045 2.045 0 0 1 2.892 0z" /></svg>',

            'pill' => '{{GRADIENT_DEF}}<svg xmlns="http://www.w3.org/2000/svg" width="{{WIDTH}}" height="{{HEIGHT}}" viewBox="0 0 24 24" fill="{{FILL}}" stroke="{{STROKE}}" stroke-width="{{STROKE_WIDTH}}" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 6m0 6a6 6 0 0 1 6 -6h6a6 6 0 0 1 6 6v0a6 6 0 0 1 -6 6h-6a6 6 0 0 1 -6 -6z" /></svg>',

            'arrow-left' => '{{GRADIENT_DEF}}<svg xmlns="http://www.w3.org/2000/svg" width="{{WIDTH}}" height="{{HEIGHT}}" viewBox="0 0 24 24" fill="{{FILL}}" stroke="{{STROKE}}" stroke-width="{{STROKE_WIDTH}}" stroke-linecap="round" stroke-linejoin="round"> <path d="M11 17h6l-4 -5l4 -5h-6l-4 5z" /></svg>',

            'arrow-right' => '{{GRADIENT_DEF}}<svg xmlns="http://www.w3.org/2000/svg" width="{{WIDTH}}" height="{{HEIGHT}}" viewBox="0 0 24 24" fill="{{FILL}}" stroke="{{STROKE}}" stroke-width="{{STROKE_WIDTH}}" stroke-linecap="round" stroke-linejoin="round"> <path d="M13 7h-6l4 5l-4 5h6l4 -5z" /></svg>'
        ];
    }
}
