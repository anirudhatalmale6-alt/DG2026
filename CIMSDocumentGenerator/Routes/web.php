<?php

use Illuminate\Support\Facades\Route;

// Dashboard / Document list
Route::get('/', 'DocgenController@index')->name('index');

// Generate document form
Route::get('/generate', 'DocgenController@create')->name('create');
Route::post('/generate', 'DocgenController@generate')->name('generate');

// Documents
Route::get('/documents/{id}', 'DocgenController@show')->name('show');
Route::get('/documents/{id}/download', 'DocgenController@download')->name('download');
Route::get('/documents/{id}/viewer', 'DocgenController@viewer')->name('viewer');
Route::post('/documents/{id}/email', 'DocgenController@email')->name('email');
Route::post('/documents/{id}/status', 'DocgenController@updateStatus')->name('status');
Route::delete('/documents/{id}', 'DocgenController@destroy')->name('destroy');

// Templates
Route::get('/templates', 'DocgenController@templates')->name('templates');
Route::get('/templates/create', 'DocgenController@templateCreate')->name('templates.create');
Route::post('/templates', 'DocgenController@templateStore')->name('templates.store');
Route::get('/templates/{id}/edit', 'DocgenController@templateEdit')->name('templates.edit');
Route::put('/templates/{id}', 'DocgenController@templateUpdate')->name('templates.update');
Route::delete('/templates/{id}', 'DocgenController@templateDestroy')->name('templates.destroy');

// Template pages
Route::post('/templates/{id}/pages', 'DocgenController@pageStore')->name('pages.store');
Route::put('/pages/{id}', 'DocgenController@pageUpdate')->name('pages.update');
Route::delete('/pages/{id}', 'DocgenController@pageDestroy')->name('pages.destroy');
Route::post('/pages/reorder', 'DocgenController@pageReorder')->name('pages.reorder');

// Field mappings
Route::get('/pages/{id}/fields', 'DocgenController@fields')->name('fields');
Route::post('/pages/{id}/fields', 'DocgenController@fieldStore')->name('fields.store');
Route::put('/fields/{id}', 'DocgenController@fieldUpdate')->name('fields.update');
Route::delete('/fields/{id}', 'DocgenController@fieldDestroy')->name('fields.destroy');

// Settings
Route::get('/settings', 'DocgenController@settings')->name('settings');
Route::post('/settings', 'DocgenController@settingsSave')->name('settings.save');

// SMTP Settings
Route::get('/smtp', 'DocgenController@smtp')->name('smtp');
Route::post('/smtp', 'DocgenController@smtpSave')->name('smtp.save');
Route::post('/smtp/test', 'DocgenController@smtpTest')->name('smtp.test');

// AJAX helpers
Route::get('/api/clients', 'DocgenController@apiClients')->name('api.clients');
Route::get('/api/client/{id}', 'DocgenController@apiClientDetail')->name('api.client');
