<?php

namespace Modules\CIMS_EMP201\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $moduleNamespace = 'Modules\\CIMS_EMP201\\Http\\Controllers';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('cims/emp201')
            ->name('cimsemp201.')
            ->namespace($this->moduleNamespace)
            ->group(module_path('CIMS_EMP201', '/Routes/web.php'));
    }
}
