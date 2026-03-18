<?php

use Illuminate\Support\Facades\Route;
use Modules\JobCards\Http\Controllers\JobCardController;
use Modules\JobCards\Http\Controllers\JobCardAdminController;

Route::middleware(['web', 'auth'])
    ->prefix('job-cards')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [JobCardController::class, 'dashboard'])->name('jobcards.dashboard');

        // Job Cards CRUD
        Route::get('/', [JobCardController::class, 'index'])->name('jobcards.index');
        Route::get('/create', [JobCardController::class, 'create'])->name('jobcards.create');
        Route::post('/', [JobCardController::class, 'store'])->name('jobcards.store');
        Route::get('/{id}', [JobCardController::class, 'show'])->name('jobcards.show');
        Route::put('/{id}', [JobCardController::class, 'update'])->name('jobcards.update');
        Route::delete('/{id}', [JobCardController::class, 'destroy'])->name('jobcards.destroy');

        // Job Card Actions
        Route::post('/{id}/update-step', [JobCardController::class, 'updateStep'])->name('jobcards.updateStep');
        Route::post('/{id}/update-status', [JobCardController::class, 'updateStatus'])->name('jobcards.updateStatus');
        Route::post('/{id}/upload-document', [JobCardController::class, 'uploadDocument'])->name('jobcards.uploadDocument');
        Route::post('/{id}/generate-pack', [JobCardController::class, 'generatePack'])->name('jobcards.generatePack');
        Route::get('/{id}/download-pack/{type}', [JobCardController::class, 'downloadPack'])->name('jobcards.downloadPack');
        Route::post('/{id}/email-pack', [JobCardController::class, 'emailPack'])->name('jobcards.emailPack');

        // Beneficial Ownership endpoints
        Route::get('/{id}/bo-directors', [JobCardController::class, 'getDirectors'])->name('jobcards.boDirectors');
        Route::post('/{id}/bo-fetch-id', [JobCardController::class, 'fetchIdDocument'])->name('jobcards.boFetchId');
        Route::post('/{id}/bo-upload-id', [JobCardController::class, 'uploadIdDocument'])->name('jobcards.boUploadId');
        Route::post('/{id}/bo-fetch-poa', [JobCardController::class, 'fetchPOA'])->name('jobcards.boFetchPoa');
        Route::post('/{id}/bo-upload-poa', [JobCardController::class, 'uploadPOA'])->name('jobcards.boUploadPoa');
        Route::post('/{id}/bo-generate-cra01', [JobCardController::class, 'generateCRA01'])->name('jobcards.boGenerateCra01');
        Route::post('/{id}/bo-generate-doc', [JobCardController::class, 'generateBODocument'])->name('jobcards.boGenerateDoc');
        Route::get('/{id}/bo-review', [JobCardController::class, 'getBOReview'])->name('jobcards.boReview');
        Route::get('/{id}/bo-attachment/{attachmentId}', [JobCardController::class, 'downloadBOAttachment'])->name('jobcards.boAttachment');
        Route::post('/{id}/bo-email', [JobCardController::class, 'emailBODocuments'])->name('jobcards.boEmail');

        // AJAX endpoints
        Route::get('/api/client-info/{clientId}', [JobCardController::class, 'getClientInfo'])->name('jobcards.clientInfo');
        Route::get('/api/client-search', [JobCardController::class, 'searchClients'])->name('jobcards.clientSearch');
        Route::get('/api/job-type-config/{typeId}', [JobCardController::class, 'getJobTypeConfig'])->name('jobcards.typeConfig');
        Route::get('/api/dashboard-stats', [JobCardController::class, 'dashboardStats'])->name('jobcards.dashboardStats');

        // Admin Setup
        Route::prefix('admin')->group(function () {
            // Job Types
            Route::get('/types', [JobCardAdminController::class, 'types'])->name('jobcards.admin.types');
            Route::post('/types', [JobCardAdminController::class, 'storeType'])->name('jobcards.admin.storeType');
            Route::put('/types/{id}', [JobCardAdminController::class, 'updateType'])->name('jobcards.admin.updateType');
            Route::delete('/types/{id}', [JobCardAdminController::class, 'deleteType'])->name('jobcards.admin.deleteType');

            // Steps per type
            Route::get('/types/{typeId}/steps', [JobCardAdminController::class, 'steps'])->name('jobcards.admin.steps');
            Route::post('/types/{typeId}/steps', [JobCardAdminController::class, 'storeStep'])->name('jobcards.admin.storeStep');
            Route::put('/steps/{id}', [JobCardAdminController::class, 'updateStep'])->name('jobcards.admin.updateStep');
            Route::delete('/steps/{id}', [JobCardAdminController::class, 'deleteStep'])->name('jobcards.admin.deleteStep');
            Route::post('/types/{typeId}/steps/reorder', [JobCardAdminController::class, 'reorderSteps'])->name('jobcards.admin.reorderSteps');

            // Fields per type
            Route::get('/types/{typeId}/fields', [JobCardAdminController::class, 'fields'])->name('jobcards.admin.fields');
            Route::post('/types/{typeId}/fields', [JobCardAdminController::class, 'saveFields'])->name('jobcards.admin.saveFields');

            // Document requirements per type
            Route::get('/types/{typeId}/documents', [JobCardAdminController::class, 'documents'])->name('jobcards.admin.documents');
            Route::post('/types/{typeId}/documents', [JobCardAdminController::class, 'storeDocument'])->name('jobcards.admin.storeDocument');
            Route::put('/documents/{id}', [JobCardAdminController::class, 'updateDocument'])->name('jobcards.admin.updateDocument');
            Route::delete('/documents/{id}', [JobCardAdminController::class, 'deleteDocument'])->name('jobcards.admin.deleteDocument');
        });
    });
