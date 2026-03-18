<?php

use Illuminate\Support\Facades\Route;
use Modules\CIMSCore\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| CIMSCore Web Routes
|--------------------------------------------------------------------------
| Main entry point for CIMS system
*/

// Welcome page - clean white page with POPIA consent
Route::get('/', [WelcomeController::class, 'index'])->name('cimscore.welcome');
Route::get('/welcome', [WelcomeController::class, 'index'])->name('cimscore.home');

// Main Landing page - full CIMS dashboard with header, menu, footer
Route::get('/landing', [WelcomeController::class, 'landing'])->name('cimscore.landing');

// Wizard page - after consent, shows menu and wizard tabs
Route::get('/wizard', [WelcomeController::class, 'wizard'])->name('cimscore.wizard');

// Hello Sunshine page
Route::get('/hello', [WelcomeController::class, 'hello'])->name('cimscore.hello');

// Client Portal page
Route::get('/client-portal', [WelcomeController::class, 'clientportal'])->name('cimscore.clientportal');
