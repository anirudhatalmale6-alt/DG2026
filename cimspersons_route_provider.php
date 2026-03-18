<?php

namespace Modules\CIMSPersons\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CIMSPersonsServiceProvider extends ServiceProvider
{
    protected $moduleName = 'CIMSPersons';
    protected $moduleNameLower = 'cimspersons';

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
