<?php

namespace App\Http\Controllers\Traits\PrimeVueTest;

trait BreadcrumbTestTrait
{
    protected function getBreadcrumbWidgets(): array
    {
        return [
            [
                'type' => 'breadcrumb',
                'items' => [
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Components', 'url' => '/components'],
                    ['label' => 'PrimeVue Test'],
                ],
                'home' => ['icon' => 'pi pi-home', 'url' => '/']
            ]
        ];
    }
}
