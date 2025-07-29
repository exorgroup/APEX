<?php

namespace App\Http\Controllers\Traits\PrimeVueTest;

trait BreadcrumbTestTrait
{
    protected function getBreadcrumbWidgets(): array
    {
        return [
            // Pro breadcrumb with icons and custom separator
            [
                'type' => 'breadcrumb',
                'items' => [
                    ['label' => 'Electronics', 'icon' => 'pi pi-sitemap', 'url' => '/electronics'],
                    ['label' => 'Computer', 'icon' => 'pi pi-desktop', 'url' => '/electronics/computer'],
                    ['label' => 'Accessories', 'icon' => 'pi pi-cog', 'url' => '/electronics/computer/accessories'],
                    ['label' => 'Keyboard', 'icon' => 'pi pi-th-large'],
                ],
                'home' => ['icon' => 'pi pi-desktop', 'url' => '/'],
                'separator' => ' > ',
                'pointer' => true
            ],
        ];
    }
}
