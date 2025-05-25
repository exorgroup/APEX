<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        // dd(\App\Models\User::all());
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });

    Route::get('/apex-test', function () {
        return view('apex-test');
    })->name('apex.test');

    Route::get('/apex-widget-test', function () {
        return view('apex-widget-test', [
            'pageTitle' => 'APEX Widget Test Page',
            'testData' => [
                'dynamic_title' => 'Dynamic Title from Controller',
                'dynamic_content' => 'This content was passed from the controller.'
            ]
        ]);
    })->name('apex.widget.test');


    Route::get('/test-widget-registry', function () {
        $registry = app(\ExorGroup\Apex\WidgetRegistry::class);

        return [
            'imageTub_exists' => $registry->exists('imageTub'),
            'imageTub_class' => $registry->resolve('imageTub'),
            'infoTub_exists' => $registry->exists('infoTub'),
            'infoTub_class' => $registry->resolve('infoTub'),
            'all_registered' => $registry->getRegistered(),
            'license_valid' => app('apex.pro.license')->validate()
        ];
    });

    Route::get('/test-imagetub-direct', function () {
        try {
            $widget = new \ExorGroup\ApexPro\Widgets\ImageTubWidget();
            $result = $widget->render([
                'image' => 'https://example.com/food-image.jpg',
                'title' => 'T-Bone Steak',
                'titleUrl' => '/menu/t-bone-steak',
                'line1' => '16 mins to cook',
                'line2' => '$16.50',
                'width' => '300px',
                'imageSize' => '200px'
            ]);
            return response($result)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    });

    Route::get('/test-view-loading', function () {
        try {
            // Test if the view exists
            $viewExists = view()->exists('apexpro::widgets.image-tub');

            // Try to render a simple version
            if ($viewExists) {
                $content = view('apexpro::widgets.image-tub', [
                    'id' => 'test-widget',
                    'image' => 'https://example.com/test.jpg',
                    'title' => 'Test Title',
                    'line1' => 'Test Line 1',
                    'line2' => 'Test Line 2',
                    'line3' => '',
                    'width' => '300px',
                    'height' => 'auto',
                    'layout' => 'vertical',
                    'imageSize' => '200px',
                    'titleUrl' => '#',
                    'titleTarget' => '_self',
                    'borderRadius' => '12px',
                    'backgroundColor' => '#ffffff',
                    'shadow' => true,
                    'padding' => '1.5rem',
                    'titleClass' => 'image-tub-title',
                    'line1Class' => 'image-tub-line1',
                    'line2Class' => 'image-tub-line2',
                    'line3Class' => 'image-tub-line3',
                    'containerClass' => '',
                    'imageAlt' => 'Test Image'
                ])->render();

                return response($content)->header('Content-Type', 'text/html');
            } else {
                return response()->json([
                    'view_exists' => false,
                    'checked_paths' => [
                        resource_path('views/vendor/apexpro/widgets/image-tub.blade.php'),
                        resource_path('views/apexpro/widgets/image-tub.blade.php'),
                        base_path('ExorGroup/ApexPro/resources/views/widgets/image-tub.blade.php')
                    ],
                    'path_exists' => [
                        'vendor_apexpro' => is_dir(resource_path('views/vendor/apexpro')),
                        'apexpro' => is_dir(resource_path('views/apexpro')),
                        'package' => is_dir(base_path('ExorGroup/ApexPro/resources/views'))
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    });
});
