<?php

use Illuminate\Support\Facades\Route;
use Modules\CIMSClients\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| CIMSClients Web Routes
|--------------------------------------------------------------------------
|
| Routes are prefixed with /cims/clients and named cimsclients.*
|
*/

// Main CRUD
Route::get('/', [ClientController::class, 'index'])->name('index');
Route::get('/search', [ClientController::class, 'search'])->name('search');
Route::get('/create', [ClientController::class, 'create'])->name('create');
Route::post('/', [ClientController::class, 'store'])->name('store');
Route::get('/{id}', [ClientController::class, 'show'])->name('show')->where('id', '[0-9]+');
Route::get('/{id}/edit', [ClientController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
Route::put('/{id}', [ClientController::class, 'update'])->name('update')->where('id', '[0-9]+');
Route::delete('/{id}', [ClientController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');

// Status management
Route::get('/{id}/toggle', [ClientController::class, 'toggle'])->name('toggle.get')->where('id', '[0-9]+');
Route::post('/{id}/toggle', [ClientController::class, 'toggle'])->name('toggle')->where('id', '[0-9]+');
Route::post('/{id}/restore', [ClientController::class, 'restore'])->name('restore')->where('id', '[0-9]+');
Route::delete('/{id}/force-delete', [ClientController::class, 'forceDelete'])->name('forceDelete')->where('id', '[0-9]+');

// Utilities
Route::post('/check-duplicate', [ClientController::class, 'checkDuplicate'])->name('check-duplicate');
Route::post('/generate-code', [ClientController::class, 'generateCode'])->name('generate-code');

// SIC Codes
Route::get('/sic-categories', [ClientController::class, 'sicCategories'])->name('sic-categories');
Route::get('/sic-natures', [ClientController::class, 'sicNatures'])->name('sic-natures');

// Directors
Route::get('/directors', [ClientController::class, 'directors'])->name('directors');
Route::post('/directors', [ClientController::class, 'storeDirector'])->name('directors.store');
Route::delete('/directors/{id}', [ClientController::class, 'deleteDirector'])->name('directors.delete');

// Client Addresses
Route::get('/addresses', [ClientController::class, 'clientAddresses'])->name('addresses');
Route::post('/addresses', [ClientController::class, 'storeClientAddress'])->name('addresses.store');
Route::delete('/addresses/{id}', [ClientController::class, 'deleteClientAddress'])->name('addresses.delete');
