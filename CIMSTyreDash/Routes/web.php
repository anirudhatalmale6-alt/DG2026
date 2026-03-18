<?php

use Illuminate\Support\Facades\Route;
use Modules\CIMSTyreDash\Http\Controllers\DashboardController;
use Modules\CIMSTyreDash\Http\Controllers\CatalogueController;
use Modules\CIMSTyreDash\Http\Controllers\BrandController;
use Modules\CIMSTyreDash\Http\Controllers\ServiceController;
use Modules\CIMSTyreDash\Http\Controllers\QuoteController;
use Modules\CIMSTyreDash\Http\Controllers\JobCardController;
use Modules\CIMSTyreDash\Http\Controllers\StockController;
use Modules\CIMSTyreDash\Http\Controllers\CustomerController;
use Modules\CIMSTyreDash\Http\Controllers\BranchController;
use Modules\CIMSTyreDash\Http\Controllers\SettingsController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// --- AJAX Routes (must come before parameterized routes) ---
Route::get('ajax/size-search', [CatalogueController::class, 'sizeSearch'])->name('ajax.size-search');
Route::get('ajax/search-products', [CatalogueController::class, 'ajaxSearchProducts'])->name('ajax.search-products');
Route::get('ajax/search-customers', [QuoteController::class, 'ajaxSearchCustomers'])->name('ajax.search-customers');
Route::get('ajax/search-vehicles', [QuoteController::class, 'ajaxSearchVehicles'])->name('ajax.search-vehicles');
Route::get('ajax/stock-by-size', [StockController::class, 'ajaxStockBySize'])->name('ajax.stock-by-size');

// --- Catalogue ---
Route::get('catalogue', [CatalogueController::class, 'index'])->name('catalogue.index');
Route::get('catalogue/create', [CatalogueController::class, 'create'])->name('catalogue.create');
Route::post('catalogue/store', [CatalogueController::class, 'store'])->name('catalogue.store');
Route::get('catalogue/{id}/edit', [CatalogueController::class, 'edit'])->name('catalogue.edit');
Route::put('catalogue/{id}/update', [CatalogueController::class, 'update'])->name('catalogue.update');
Route::delete('catalogue/{id}/delete', [CatalogueController::class, 'destroy'])->name('catalogue.destroy');

// --- Brands ---
Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
Route::post('brands/store', [BrandController::class, 'store'])->name('brands.store');
Route::get('brands/{id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
Route::put('brands/{id}/update', [BrandController::class, 'update'])->name('brands.update');
Route::delete('brands/{id}/delete', [BrandController::class, 'destroy'])->name('brands.destroy');
Route::put('brands/{id}/activate', [BrandController::class, 'activate'])->name('brands.activate');
Route::put('brands/{id}/deactivate', [BrandController::class, 'deactivate'])->name('brands.deactivate');

// --- Services ---
Route::get('services', [ServiceController::class, 'index'])->name('services.index');
Route::get('services/create', [ServiceController::class, 'create'])->name('services.create');
Route::post('services/store', [ServiceController::class, 'store'])->name('services.store');
Route::get('services/{id}/edit', [ServiceController::class, 'edit'])->name('services.edit');
Route::put('services/{id}/update', [ServiceController::class, 'update'])->name('services.update');
Route::delete('services/{id}/delete', [ServiceController::class, 'destroy'])->name('services.destroy');
Route::put('services/{id}/activate', [ServiceController::class, 'activate'])->name('services.activate');
Route::put('services/{id}/deactivate', [ServiceController::class, 'deactivate'])->name('services.deactivate');

// --- Quotes ---
Route::get('quotes', [QuoteController::class, 'index'])->name('quotes.index');
Route::get('quotes/create', [QuoteController::class, 'create'])->name('quotes.create');
Route::post('quotes/store', [QuoteController::class, 'store'])->name('quotes.store');
Route::get('quotes/{id}', [QuoteController::class, 'show'])->name('quotes.show');
Route::get('quotes/{id}/edit', [QuoteController::class, 'edit'])->name('quotes.edit');
Route::put('quotes/{id}/update', [QuoteController::class, 'update'])->name('quotes.update');
Route::delete('quotes/{id}/delete', [QuoteController::class, 'destroy'])->name('quotes.destroy');
Route::put('quotes/{id}/status', [QuoteController::class, 'updateStatus'])->name('quotes.status');
Route::post('quotes/{id}/convert-job-card', [QuoteController::class, 'convertToJobCard'])->name('quotes.convert-job-card');
Route::get('quotes/{id}/pdf', [QuoteController::class, 'pdf'])->name('quotes.pdf');

// --- Job Cards ---
Route::get('job-cards', [JobCardController::class, 'index'])->name('jobcards.index');
Route::get('job-cards/create', [JobCardController::class, 'create'])->name('jobcards.create');
Route::post('job-cards/store', [JobCardController::class, 'store'])->name('jobcards.store');
Route::get('job-cards/{id}', [JobCardController::class, 'show'])->name('jobcards.show');
Route::get('job-cards/{id}/edit', [JobCardController::class, 'edit'])->name('jobcards.edit');
Route::put('job-cards/{id}/update', [JobCardController::class, 'update'])->name('jobcards.update');
Route::delete('job-cards/{id}/delete', [JobCardController::class, 'destroy'])->name('jobcards.destroy');
Route::put('job-cards/{id}/status', [JobCardController::class, 'updateStatus'])->name('jobcards.status');
Route::post('job-cards/{id}/close-invoice', [JobCardController::class, 'closeToInvoice'])->name('jobcards.close-invoice');

// --- Stock ---
Route::get('stock', [StockController::class, 'index'])->name('stock.index');
Route::get('stock/{id}/adjust', [StockController::class, 'adjust'])->name('stock.adjust');
Route::post('stock/{id}/adjust', [StockController::class, 'processAdjustment'])->name('stock.process-adjustment');
Route::get('stock/transfer', [StockController::class, 'transfer'])->name('stock.transfer');
Route::post('stock/transfer', [StockController::class, 'processTransfer'])->name('stock.process-transfer');

// --- Customers ---
Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::post('customers/store', [CustomerController::class, 'store'])->name('customers.store');
Route::get('customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
Route::get('customers/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::put('customers/{id}/update', [CustomerController::class, 'update'])->name('customers.update');
Route::delete('customers/{id}/delete', [CustomerController::class, 'destroy'])->name('customers.destroy');

// --- Branches ---
Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
Route::get('branches/create', [BranchController::class, 'create'])->name('branches.create');
Route::post('branches/store', [BranchController::class, 'store'])->name('branches.store');
Route::get('branches/{id}/edit', [BranchController::class, 'edit'])->name('branches.edit');
Route::put('branches/{id}/update', [BranchController::class, 'update'])->name('branches.update');
Route::delete('branches/{id}/delete', [BranchController::class, 'destroy'])->name('branches.destroy');
Route::put('branches/{id}/activate', [BranchController::class, 'activate'])->name('branches.activate');
Route::put('branches/{id}/deactivate', [BranchController::class, 'deactivate'])->name('branches.deactivate');

// --- Settings ---
Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('settings/update', [SettingsController::class, 'update'])->name('settings.update');
