<?php

namespace Modules\Zoho\Providers;

use Illuminate\Support\ServiceProvider;

class ZohoServiceProvider extends ServiceProvider
{
    public function register()
    {
        // This tells Laravel to load the config file we just made
        $this->mergeConfigFrom(
            base_path('Modules/Zoho/Config/config.php'), 'zoho'
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(module_path('Zoho', 'routes/web.php'));

        $this->loadViewsFrom(module_path('Zoho', 'Resources/views'), 'zoho');
    }
}