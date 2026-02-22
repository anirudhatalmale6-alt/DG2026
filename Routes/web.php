<?php

use Illuminate\Support\Facades\Route;
use Modules\DG2026\Http\Controllers\DocgenController;

Route::middleware(['auth'])->prefix('dg2026')->group(function () {

    // Dashboard / Document list
    Route::get('/', [DocgenController::class, 'index'])->name('dg2026.index');

    // Generate document form
    Route::get('/generate', [DocgenController::class, 'create'])->name('dg2026.create');
    Route::post('/generate', [DocgenController::class, 'generate'])->name('dg2026.generate');

    // Documents
    Route::get('/documents/{id}', [DocgenController::class, 'show'])->name('dg2026.show');
    Route::get('/documents/{id}/download', [DocgenController::class, 'download'])->name('dg2026.download');
    Route::get('/documents/{id}/viewer', [DocgenController::class, 'viewer'])->name('dg2026.viewer');
    Route::post('/documents/{id}/email', [DocgenController::class, 'email'])->name('dg2026.email');
    Route::post('/documents/{id}/status', [DocgenController::class, 'updateStatus'])->name('dg2026.status');
    Route::delete('/documents/{id}', [DocgenController::class, 'destroy'])->name('dg2026.destroy');

    // Templates
    Route::get('/templates', [DocgenController::class, 'templates'])->name('dg2026.templates');
    Route::get('/templates/create', [DocgenController::class, 'templateCreate'])->name('dg2026.templates.create');
    Route::post('/templates', [DocgenController::class, 'templateStore'])->name('dg2026.templates.store');
    Route::get('/templates/{id}/edit', [DocgenController::class, 'templateEdit'])->name('dg2026.templates.edit');
    Route::put('/templates/{id}', [DocgenController::class, 'templateUpdate'])->name('dg2026.templates.update');
    Route::delete('/templates/{id}', [DocgenController::class, 'templateDestroy'])->name('dg2026.templates.destroy');

    // Template pages
    Route::post('/templates/{id}/pages', [DocgenController::class, 'pageStore'])->name('dg2026.pages.store');
    Route::put('/pages/{id}', [DocgenController::class, 'pageUpdate'])->name('dg2026.pages.update');
    Route::delete('/pages/{id}', [DocgenController::class, 'pageDestroy'])->name('dg2026.pages.destroy');
    Route::post('/pages/reorder', [DocgenController::class, 'pageReorder'])->name('dg2026.pages.reorder');

    // Field mappings
    Route::get('/pages/{id}/fields', [DocgenController::class, 'fields'])->name('dg2026.fields');
    Route::post('/pages/{id}/fields', [DocgenController::class, 'fieldStore'])->name('dg2026.fields.store');
    Route::put('/fields/{id}', [DocgenController::class, 'fieldUpdate'])->name('dg2026.fields.update');
    Route::delete('/fields/{id}', [DocgenController::class, 'fieldDestroy'])->name('dg2026.fields.destroy');

    // Settings
    Route::get('/settings', [DocgenController::class, 'settings'])->name('dg2026.settings');
    Route::post('/settings', [DocgenController::class, 'settingsSave'])->name('dg2026.settings.save');

    // SMTP Settings
    Route::get('/smtp', [DocgenController::class, 'smtp'])->name('dg2026.smtp');
    Route::post('/smtp', [DocgenController::class, 'smtpSave'])->name('dg2026.smtp.save');
    Route::post('/smtp/test', [DocgenController::class, 'smtpTest'])->name('dg2026.smtp.test');

    // AJAX helpers
    Route::get('/api/clients', [DocgenController::class, 'apiClients'])->name('dg2026.api.clients');
    Route::get('/api/client/{id}', [DocgenController::class, 'apiClientDetail'])->name('dg2026.api.client');
});
