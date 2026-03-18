<?php

namespace Modules\CIMSCore\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CIMSCoreServiceProvider extends ServiceProvider
{
    protected $moduleName = 'CIMSCore';
    protected $moduleNameLower = 'cimscore';

    public function boot()
    {
        $this->registerViews();
        $this->registerRoutes();
    }

    public function register()
    {
        //
    }

    protected function registerViews()
    {
        $sourcePath = module_path($this->moduleName, 'Resources/views');
        $this->loadViewsFrom($sourcePath, $this->moduleNameLower);
    }

    protected function registerRoutes()
    {
        Route::middleware('web')
            ->prefix('cims')
            ->group(module_path($this->moduleName, 'Routes/web.php'));
    }

    public function provides()
    {
        return [];
    }
}
