<?php

namespace Modules\CimsDocgen\Providers;

use Illuminate\Support\ServiceProvider;

class CimsDocgenServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'CimsDocgen';
    protected string $moduleNameLower = 'cimsdocgen';

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
