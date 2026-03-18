<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerAgedAnalysis\Http\Controllers\AgedAnalysisController;

/*
|--------------------------------------------------------------------------
| Customer Aged Analysis Module Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth'])
    ->prefix('aged-analysis')
    ->group(function () {

        // Main report page
        Route::get('/', [AgedAnalysisController::class, 'index'])
            ->name('aged-analysis.index');

        // Generate report data (AJAX)
        Route::post('/generate', [AgedAnalysisController::class, 'generate'])
            ->name('aged-analysis.generate');

        // Generate and download PDF
        Route::post('/pdf', [AgedAnalysisController::class, 'pdf'])
            ->name('aged-analysis.pdf');

        // Email report
        Route::post('/email', [AgedAnalysisController::class, 'email'])
            ->name('aged-analysis.email');
    });
