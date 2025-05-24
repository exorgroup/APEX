<?php

namespace App\Apex;

use Illuminate\Support\ServiceProvider;
use ExorGroup\Apex\ApexServiceProvider as ExorGroupApexServiceProvider;

class ApexServiceProvider extends ServiceProvider
{
    protected $exorGroupProvider;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->exorGroupProvider = new ExorGroupApexServiceProvider($app);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->exorGroupProvider->register();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->exorGroupProvider->boot();
    }
}
