<?php

namespace Modules\JobCards\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register views
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'jobcards');

        // Register routes
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../Config/module.php', 'job_cards');
    }

    public function register(): void
    {
        $this->app->singleton(
            \Modules\JobCards\Services\JobCardService::class,
            function ($app) {
                return new \Modules\JobCards\Services\JobCardService();
            }
        );

        $this->app->singleton(
            \Modules\JobCards\Services\PackGeneratorService::class,
            function ($app) {
                return new \Modules\JobCards\Services\PackGeneratorService();
            }
        );
    }
}
