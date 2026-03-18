<?php

namespace Modules\CIMSDocuments\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $moduleNamespace = 'Modules\CIMSDocuments\Http\Controllers';

    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes()
    {
        Route::middleware(['web', 'auth'])
            ->namespace($this->moduleNamespace)
            ->prefix('cims/documents')
            ->name('cimsdocuments.')
            ->group(module_path('CIMSDocuments', '/Routes/web.php'));
    }
}
