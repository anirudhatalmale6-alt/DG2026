<?php

use Illuminate\Support\Facades\Route;

// Client Master CRUD
Route::get('/', 'ClientMasterController@index')->name('index');
Route::get('/create', 'ClientMasterController@create')->name('create');
Route::post('/store', 'ClientMasterController@store')->name('store');

// AJAX routes - MUST come before /{id} routes
Route::get('/ajax/addresses', 'ClientMasterController@getAddresses')->name('ajax.addresses');
Route::post('/ajax/check-company-name', 'ClientMasterController@checkCompanyName')->name('ajax.check-company-name');
Route::get('/ajax/generate-code', 'ClientMasterController@generateCode')->name('ajax.generate-code');
Route::get('/ajax/get-company-type', 'ClientMasterController@getCompanyTypeByCode')->name('ajax.get-company-type');
Route::put('/ajax/directors/{directorId}', 'ClientMasterController@updateDirector')->name('ajax.directors.update');
Route::get('/ajax/client/{id}', 'ClientMasterController@get_client')->name('ajax.client.get');
Route::get('/ajax/director/{id}', 'ClientMasterController@get_director')->name('ajax.director.get');
Route::get('/ajax/bank/{id}', 'ClientMasterController@get_bank')->name('ajax.bank.get');
Route::get('/ajax/address/{id}', 'ClientMasterController@get_address')->name('ajax.address.get');

Route::get('/document/view/{clientId}/{documentType}', 'ClientMasterController@viewDocument')->name('document.view');

Route::get('/clear/cache', 'ClientMasterController@clear_cache')->name('clear.cache');

// Routes with {id} parameter - MUST come after specific routes
Route::get('/{id}', 'ClientMasterController@show')->name('show');
Route::get('/{id}/edit', 'ClientMasterController@edit')->name('edit');
Route::put('/update/{id}', 'ClientMasterController@update')->name('update');
Route::delete('/delete/{id}', 'ClientMasterController@destroy')->name('delete');

// Restore soft-deleted
Route::put('/{id}/restore', 'ClientMasterController@restore')->name('restore');

// Activate/Deactivate
Route::put('/{id}/activate', 'ClientMasterController@activate')->name('activate');
Route::put('/{id}/deactivate', 'ClientMasterController@deactivate')->name('deactivate');

// Duplicate client
Route::get('/{id}/duplicate', 'ClientMasterController@duplicate')->name('duplicate');

// Audit history
Route::get('/{id}/audit', 'ClientMasterController@audit')->name('audit');

// Check restore (for duplicate validation)
Route::get('/{id}/check-restore', 'ClientMasterController@checkRestore')->name('check-restore');

// Address linking
Route::post('/{id}/addresses', 'ClientMasterController@linkAddress')->name('link-address');
Route::delete('/{id}/addresses/{addressId}', 'ClientMasterController@unlinkAddress')->name('unlink-address');
