<?php

namespace ExorGroup\ApexPro\Widgets;

class ImageTubWidget extends BaseProWidget
{
    protected function renderWidget(array $params): string
    {
        return $this->view('widgets.image-tub', $params);
    }

    public function getDefaults(): array
    {
        return [
            'image' => '',
            'imageAlt' => 'Image',
            'title' => 'Default Title',
            'titleUrl' => '#',
            'titleTarget' => '_self',
            'line1' => '16 mins to cook',
            'line2' => '$16.50$',
            'line3' => '',
            'line1Class' => 'image-tub-line1',
            'line2Class' => 'image-tub-line2',
            'line3Class' => 'image-tub-line3',
            'layout' => 'vertical', // 'vertical' or 'horizontal'
            'width' => '300px',
            'height' => 'auto',
            'borderRadius' => '12px',
            'backgroundColor' => '#ffffff',
            'shadow' => true,
            'padding' => '1.5rem',
            'imageSize' => '200px', // for vertical layout
            'imageWidth' => '150px', // for horizontal layout
            'imageHeight' => '120px', // for horizontal layout
            'titleClass' => 'image-tub-title',
            'containerClass' => '',
        ];
    }
}
