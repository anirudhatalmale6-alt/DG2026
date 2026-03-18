<?php

use Illuminate\Support\Facades\Route;
use Modules\CIMSDocuments\Http\Controllers\DocumentController;

/*
|--------------------------------------------------------------------------
| CIMS Documents Module Routes
|--------------------------------------------------------------------------
| Prefix: /cims/documents
| Name: cimsdocuments.
*/

// List all documents
Route::get('/', [DocumentController::class, 'index'])->name('index');

// Create new document
Route::get('/create', [DocumentController::class, 'create'])->name('create');
Route::post('/', [DocumentController::class, 'store'])->name('store');

// View single document
Route::get('/{id}', [DocumentController::class, 'show'])->name('show')->where('id', '[0-9]+');

// Edit document
Route::get('/{id}/edit', [DocumentController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
Route::put('/{id}', [DocumentController::class, 'update'])->name('update')->where('id', '[0-9]+');

// Delete document
Route::delete('/{id}', [DocumentController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');

// Download document
Route::get('/{id}/download', [DocumentController::class, 'download'])->name('download')->where('id', '[0-9]+');

// Preview document
Route::get('/{id}/preview', [DocumentController::class, 'preview'])->name('preview')->where('id', '[0-9]+');

// AJAX routes for dropdowns
Route::get('/types-by-category/{categoryId}', [DocumentController::class, 'getTypesByCategory'])->name('types.by.category');
Route::get('/clients/search', [DocumentController::class, 'searchClients'])->name('clients.search');
