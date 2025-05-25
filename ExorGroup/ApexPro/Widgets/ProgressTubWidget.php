<?php

namespace ExorGroup\ApexPro\Widgets;

class ProgressTubWidget extends BaseProWidget
{
    protected function renderWidget(array $params): string
    {
        return $this->view('widgets.progress-tub', $params);
    }

    public function getDefaults(): array
    {
        return [
            'text' => '0',
            'textClass' => '',
            'textAlign' => 'center',
            'caption' => '',
            'captionClass' => '',
            'captionAlign' => 'center',
            'gaugeType' => 'radial', // 'radial', 'round', 'linear'
            'gaugePosition' => 'top', // 'top', 'bottom'
            'gaugeColor' => '#f59e0b',
            'gaugeBackgroundColor' => '#e5e7eb',
            'gaugeValue' => 0, // 0-100
            'showPercentage' => true,
            'gaugeLabel' => '',
            'gaugeLabelClass' => '',
            'gaugeLabelAlign' => 'center', // 'left', 'center', 'right'
            'gaugeLabelPosition' => 'below', // 'above', 'below'
            'badges' => [], // Array of badge objects
            'width' => '300px',
            'height' => 'auto',
            'borderRadius' => '12px',
            'backgroundColor' => '#ffffff',
            'shadow' => true,
            'padding' => '1.5rem',
            'containerClass' => '',
            'gaugeWidth' => '150px', // Width of the gauge
            'gaugeHeight' => '8px', // Height/thickness for linear gauge
            'gaugeThickness' => '8px', // Stroke width for radial/round gauges
        ];
    }
}
