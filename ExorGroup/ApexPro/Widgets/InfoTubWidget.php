<?php

namespace ExorGroup\ApexPro\Widgets;

class InfoTubWidget extends BaseProWidget
{
    protected function renderWidget(array $params): string
    {
        return $this->view('widgets.info-tub', $params);
    }

    public function getDefaults(): array
    {
        return [
            'image' => '',
            'size' => '60px',
            'position' => 'top',
            'text' => '0',
            'caption' => 'Caption',
            'borderWidth' => '1px',
            'borderColor' => '#e5e7eb',
            'textClass' => '',
            'captionClass' => '',
            'boxClass' => '',
            'textAlign' => 'left',
            'captionAlign' => 'left',
            'imageAlign' => 'center',
        ];
    }
}
