<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TenancyServiceProvider::class, // <-- here
    App\Providers\ApexServiceProvider::class, // Add APEX Service Provider
];
