<?php

use Illuminate\Support\Facades\Route;

// Index / list
Route::get('/', 'Emp201Controller@index')->name('index');

// Create
Route::get('/create', 'Emp201Controller@create')->name('create');
Route::post('/store', 'Emp201Controller@store')->name('store');

// Show
Route::get('/{id}', 'Emp201Controller@show')->name('show');

// Edit
Route::get('/{id}/edit', 'Emp201Controller@edit')->name('edit');
Route::put('/{id}', 'Emp201Controller@update')->name('update');

// Status change
Route::post('/{id}/status', 'Emp201Controller@updateStatus')->name('status');

// Delete
Route::delete('/{id}', 'Emp201Controller@destroy')->name('destroy');

// AJAX - get client details
Route::get('/api/client/{id}', 'Emp201Controller@apiClientDetail')->name('api.client');

// AJAX - get periods
Route::get('/api/periods', 'Emp201Controller@apiPeriods')->name('api.periods');
