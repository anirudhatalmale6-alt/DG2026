<?php

namespace Modules\CIMS_BANKCONV\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $moduleNamespace = 'Modules\\CIMS_BANKCONV\\Http\\Controllers';

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
            ->prefix('cims/bankconv')
            ->name('cimsbankconv.')
            ->namespace($this->moduleNamespace)
            ->group(module_path('CIMS_BANKCONV', '/Routes/web.php'));
    }
}
