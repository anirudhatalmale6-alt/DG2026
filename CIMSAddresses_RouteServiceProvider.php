<?php

namespace Modules\CIMSAddresses\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $moduleNamespace = 'Modules\CIMSAddresses\Http\Controllers';

    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    protected function mapWebRoutes()
    {
        Route::middleware(['web', 'auth'])
            ->namespace($this->moduleNamespace)
            ->prefix('cims/addresses')
            ->name('cimsaddresses.')
            ->group(module_path('CIMSAddresses', '/Routes/web.php'));
    }

    protected function mapApiRoutes()
    {
        $apiRoutesPath = module_path('CIMSAddresses', '/Routes/api.php');
        if (file_exists($apiRoutesPath)) {
            Route::prefix('api/cims/addresses')
                ->middleware('api')
                ->namespace($this->moduleNamespace)
                ->group($apiRoutesPath);
        }
    }
}
