<?php

namespace Modules\CIMSTyreDash\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $moduleNamespace = 'Modules\\CIMSTyreDash\\Http\\Controllers';

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
            ->prefix('cims/tyredash')
            ->name('cimstyredash.')
            ->namespace($this->moduleNamespace)
            ->group(module_path('CIMSTyreDash', '/Routes/web.php'));
    }
}
