<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TenancyServiceProvider::class, // <-- here
    ExorGroup\Apex\ApexServiceProvider::class,
    ExorGroup\ApexPro\ApexProServiceProvider::class,
];
