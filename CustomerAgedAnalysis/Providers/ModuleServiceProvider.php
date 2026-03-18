<?php

namespace Modules\CustomerAgedAnalysis\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Boot the module services.
     */
    public function boot(): void
    {
        // Register views
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'agedanalysis');

        // Register routes
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../Config/module.php', 'customer_aged_analysis');
    }

    /**
     * Register the module services.
     */
    public function register(): void
    {
        $this->app->singleton(\Modules\CustomerAgedAnalysis\Services\AgedAnalysisService::class);
    }
}
