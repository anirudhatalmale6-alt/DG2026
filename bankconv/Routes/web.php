<?php

use Illuminate\Support\Facades\Route;

// Index - conversion history
Route::get('/', 'BankConvController@index')->name('index');

// Unified bank conversion page
Route::get('/convert', 'BankConvController@convert')->name('convert');

// Redirect old bank-specific routes to unified page
Route::get('/fnb', function() { return redirect()->route('cimsbankconv.convert'); });
Route::get('/standard', function() { return redirect()->route('cimsbankconv.convert'); });
Route::get('/absa', function() { return redirect()->route('cimsbankconv.convert'); });
Route::get('/nedbank', function() { return redirect()->route('cimsbankconv.convert'); });
Route::get('/capitec', function() { return redirect()->route('cimsbankconv.convert'); });

// AJAX - parse bank statement text (unified — bank_type sent in request)
Route::post('/api/parse-statement', 'BankConvController@apiParseStatement')->name('api.parse-statement');

// AJAX - save conversion record
Route::post('/api/save-conversion', 'BankConvController@apiSaveConversion')->name('api.save-conversion');
