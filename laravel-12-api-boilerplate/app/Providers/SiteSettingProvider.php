<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SiteSettingService;

class SiteSettingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('SiteSetting', function ($app) {
            return new SiteSettingService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
