<?php

use Illuminate\Support\Facades\Route;
use Modules\DG2026\Http\Controllers\DocgenController;

Route::middleware(['auth'])->prefix('cims/docgen')->group(function () {

    // Dashboard / Document list
    Route::get('/', [DocgenController::class, 'index'])->name('docgen.index');

    // Generate document form
    Route::get('/generate', [DocgenController::class, 'create'])->name('docgen.create');
    Route::post('/generate', [DocgenController::class, 'generate'])->name('docgen.generate');

    // Documents
    Route::get('/documents/{id}', [DocgenController::class, 'show'])->name('docgen.show');
    Route::get('/documents/{id}/download', [DocgenController::class, 'download'])->name('docgen.download');
    Route::get('/documents/{id}/viewer', [DocgenController::class, 'viewer'])->name('docgen.viewer');
    Route::post('/documents/{id}/email', [DocgenController::class, 'email'])->name('docgen.email');
    Route::post('/documents/{id}/status', [DocgenController::class, 'updateStatus'])->name('docgen.status');
    Route::delete('/documents/{id}', [DocgenController::class, 'destroy'])->name('docgen.destroy');

    // Templates
    Route::get('/templates', [DocgenController::class, 'templates'])->name('docgen.templates');
    Route::get('/templates/create', [DocgenController::class, 'templateCreate'])->name('docgen.templates.create');
    Route::post('/templates', [DocgenController::class, 'templateStore'])->name('docgen.templates.store');
    Route::get('/templates/{id}/edit', [DocgenController::class, 'templateEdit'])->name('docgen.templates.edit');
    Route::put('/templates/{id}', [DocgenController::class, 'templateUpdate'])->name('docgen.templates.update');
    Route::delete('/templates/{id}', [DocgenController::class, 'templateDestroy'])->name('docgen.templates.destroy');

    // Template pages
    Route::post('/templates/{id}/pages', [DocgenController::class, 'pageStore'])->name('docgen.pages.store');
    Route::put('/pages/{id}', [DocgenController::class, 'pageUpdate'])->name('docgen.pages.update');
    Route::delete('/pages/{id}', [DocgenController::class, 'pageDestroy'])->name('docgen.pages.destroy');
    Route::post('/pages/reorder', [DocgenController::class, 'pageReorder'])->name('docgen.pages.reorder');

    // Field mappings
    Route::get('/pages/{id}/fields', [DocgenController::class, 'fields'])->name('docgen.fields');
    Route::post('/pages/{id}/fields', [DocgenController::class, 'fieldStore'])->name('docgen.fields.store');
    Route::put('/fields/{id}', [DocgenController::class, 'fieldUpdate'])->name('docgen.fields.update');
    Route::delete('/fields/{id}', [DocgenController::class, 'fieldDestroy'])->name('docgen.fields.destroy');

    // Settings
    Route::get('/settings', [DocgenController::class, 'settings'])->name('docgen.settings');
    Route::post('/settings', [DocgenController::class, 'settingsSave'])->name('docgen.settings.save');

    // SMTP Settings
    Route::get('/smtp', [DocgenController::class, 'smtp'])->name('docgen.smtp');
    Route::post('/smtp', [DocgenController::class, 'smtpSave'])->name('docgen.smtp.save');
    Route::post('/smtp/test', [DocgenController::class, 'smtpTest'])->name('docgen.smtp.test');

    // AJAX helpers
    Route::get('/api/clients', [DocgenController::class, 'apiClients'])->name('docgen.api.clients');
    Route::get('/api/client/{id}', [DocgenController::class, 'apiClientDetail'])->name('docgen.api.client');
});
