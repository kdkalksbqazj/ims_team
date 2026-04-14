<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class ModularViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register view namespaces
        View::addNamespace('iam', resource_path('views/modules/iam'));
        View::addNamespace('catalog', resource_path('views/modules/catalog'));
        View::addNamespace('organization', resource_path('views/modules/organization'));
        View::addNamespace('operations', resource_path('views/modules/operations'));
        View::addNamespace('analytics', resource_path('views/modules/analytics'));

        // Register global components from IAM module
        Blade::anonymousComponentPath(resource_path('views/modules/iam/components'));
    }
}
