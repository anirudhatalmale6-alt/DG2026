<?php

use Illuminate\Support\Facades\Route;

// Index / list
Route::get('/', 'Emp201Controller@index')->name('index');

// Pivot Table
Route::get('/pivot', 'Emp201Controller@pivot')->name('pivot');

// Export pivot to Excel (.xlsx)
Route::get('/pivot/export-excel', 'Emp201Controller@exportPivotExcel')->name('pivot.export-excel');

// Statement of Account (EMPSA)
Route::get('/statement', 'Emp201Controller@statement')->name('statement');

// AJAX - get statement data
Route::get('/api/statement-data', 'Emp201Controller@apiStatementData')->name('api.statement-data');

// Export statement to Excel (.xlsx)
Route::get('/statement/export-excel', 'Emp201Controller@exportStatementExcel')->name('statement.export-excel');

// Generate statement PDF (stores to document system, returns document viewer URL)
Route::post('/statement/generate-pdf', 'Emp201Controller@generateStatementPdf')->name('statement.generate-pdf');

// Send statement via email
Route::post('/statement/send-email', 'Emp201Controller@sendStatementEmail')->name('statement.send-email');

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

// AJAX - get SARS representative for a client
Route::get('/api/sars-representative/{clientId}', 'Emp201Controller@apiSarsRepresentative')->name('api.sars-representative');

// AJAX - get pivot data
Route::get('/api/pivot-data', 'Emp201Controller@apiPivotData')->name('api.pivot-data');

// AJAX - check duplicate declaration
Route::get('/api/check-duplicate', 'Emp201Controller@apiCheckDuplicate')->name('api.check-duplicate');

// AJAX - log audit entry
Route::post('/api/audit-log', 'Emp201Controller@apiAuditLog')->name('api.audit-log');
