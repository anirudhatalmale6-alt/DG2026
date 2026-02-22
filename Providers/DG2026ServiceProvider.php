<?php

namespace Modules\DG2026\Providers;

use Illuminate\Support\ServiceProvider;

class DG2026ServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'DG2026';
    protected string $moduleNameLower = 'dg2026';

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->loadViewsFrom(module_path($this->moduleName, 'Resources/views'), $this->moduleNameLower);
        $this->loadRoutesFrom(module_path($this->moduleName, 'Routes/web.php'));
    }

    public function register(): void
    {
        //
    }
}
