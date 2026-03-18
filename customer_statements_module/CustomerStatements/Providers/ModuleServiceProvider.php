<?php

namespace Modules\CustomerStatements\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Boot the module services.
     */
    public function boot(): void
    {
        // Register views
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'statements');

        // Register routes
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../Config/module.php', 'customer_statements');
    }

    /**
     * Register the module services.
     */
    public function register(): void
    {
        // Bind services into the container
        $this->app->singleton(\Modules\CustomerStatements\Services\StatementService::class);
        $this->app->singleton(\Modules\CustomerStatements\Services\AgingService::class);
    }
}
