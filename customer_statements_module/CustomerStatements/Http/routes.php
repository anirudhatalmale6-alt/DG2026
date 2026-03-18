<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerStatements\Http\Controllers\StatementController;

/*
|--------------------------------------------------------------------------
| Customer Statements Module Routes
|--------------------------------------------------------------------------
|
| Routes for the Customer Statements module. These are wrapped in
| the 'web' and 'auth' middleware groups to ensure only authenticated
| users can access them.
|
*/

Route::middleware(['web', 'auth'])
    ->prefix('statements')
    ->group(function () {

        // Main page with client selector and date range
        Route::get('/', [StatementController::class, 'index'])
            ->name('statements.index');

        // Generate statement (form submission)
        Route::post('/generate', [StatementController::class, 'generate'])
            ->name('statements.generate');

        // Download PDF (legacy stream - kept for backwards compat)
        Route::get('/{client_id}/pdf', [StatementController::class, 'pdf'])
            ->name('statements.pdf')
            ->where('client_id', '[0-9]+');

        // Generate PDF, store in document system, return viewer URL
        Route::post('/generate-pdf', [StatementController::class, 'generateStatementPdf'])
            ->name('statements.generate-pdf');

        // Email statement to client
        Route::post('/{client_id}/email', [StatementController::class, 'email'])
            ->name('statements.email')
            ->where('client_id', '[0-9]+');
    });
