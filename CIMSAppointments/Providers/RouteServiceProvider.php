<?php

namespace Modules\CIMSAppointments\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $moduleNamespace = 'Modules\\CIMSAppointments\\Http\\Controllers';

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
            ->prefix('cims/appointments')
            ->name('cimsappointments.')
            ->namespace($this->moduleNamespace)
            ->group(module_path('CIMSAppointments', '/Routes/web.php'));
    }
}
