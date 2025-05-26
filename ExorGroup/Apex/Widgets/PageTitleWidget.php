<?php

namespace ExorGroup\Apex\Widgets;

class PageTitleWidget extends BaseWidget
{
    protected function renderWidget(array $params): string
    {
        return $this->view('widgets.page-title', $params);
    }

    public function getDefaults(): array
    {
        return [
            'title' => 'Page Title',
            'titleClass' => '',
            'breadcrumbs' => [],
            'breadcrumbClass' => '',
            'delimiter' => '~',
            'shadow' => true,
            'border' => true,
            'borderWidth' => '1px',
            'borderColor' => '#e5e7eb',
            'backgroundColor' => '#ffffff',
            'padding' => '1rem 1.5rem',
            'borderRadius' => '8px',
            'containerClass' => '',
        ];
    }
}
