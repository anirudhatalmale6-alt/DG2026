<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CimsDirectorController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\CipcCompanyStatusController;
use App\Http\Controllers\ClientMasterController;
use App\Http\Controllers\ComplianceCenterController;
use App\Http\Controllers\DocManagerController;
use App\Http\Controllers\InfoDocsController;
use App\Http\Controllers\NsdcPersonController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\RefBankController;
use App\Http\Controllers\SarsRepController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/welcome');
Route::get('login', [SessionController::class, 'login'])->name('login')->middleware('guest');
Route::post('login', [SessionController::class, 'attemptLogin'])->name('attempt.login')->middleware('guest');

Route::post('logout', [SessionController::class, 'destroy'])->name('logout')->middleware('auth');

Route::get('/', function () {
    return view('auth.login');
    });


    
Route::get('/welcome', [SessionController::class, 'welcome'])->name('welcome')->middleware('auth');
Route::get('/landing', [SessionController::class, 'landing'])->name('landing')->middleware('auth');
// Client Master
Route::get('/client', [ClientMasterController::class, 'index'])->name('client.index')->middleware('auth');;
Route::get('client/create', [ClientMasterController::class, 'create'])->name('client.create')->middleware('auth');;
Route::post('client/store', [ClientMasterController::class, 'store'])->name('client.store')->middleware('auth');;


// AJAX routes - MUST come before /{id} routes
Route::get('ajax/addresses', [ClientMasterController::class, 'getAddresses'])->name('ajax.addresses')->middleware('auth');;
Route::post('ajax/check-company-name', [ClientMasterController::class, 'checkCompanyName'])->name('ajax.check-company-name')->middleware('auth');;
Route::get('ajax/generate-code', [ClientMasterController::class, 'generateCode'])->name('ajax.generate-code')->middleware('auth');;
Route::get('ajax/get-company-type', [ClientMasterController::class, 'getCompanyTypeByCode'])->name('ajax.get-company-type')->middleware('auth');;
Route::get('ajax/bank/{id}', [RefBankController::class, 'get_bank'])->name('ajax.bank.get')->middleware('auth');;
Route::get('ajax/address/{id}', [AddressController::class, 'get_address'])->name('ajax.address.get')->middleware('auth');;
Route::put('ajax/directors/{directorId}', [ClientMasterController::class, 'updateDirector'])->name('ajax.directors.update')->middleware('auth');;
Route::get('ajax/client/{id}', [ClientMasterController::class, 'get_client'])->name('ajax.client.get')->middleware('auth');;
Route::get('ajax/director/{id}', [ClientMasterController::class, 'get_director'])->name('ajax.director.get')->middleware('auth');;

// Signature AJAX routes
Route::post('ajax/signature/store', [SignatureController::class, 'store'])->name('ajax.signature.store')->middleware('auth');;
Route::get('ajax/signature/{clientId}', [SignatureController::class, 'show'])->name('ajax.signature.show')->middleware('auth');;
Route::delete('ajax/signature/{clientId}', [SignatureController::class, 'destroy'])->name('ajax.signature.destroy')->middleware('auth');;

Route::get('clear/cache', [ClientMasterController::class, 'clear_cache'])->name('clear.cache')->middleware('auth');;

// Demo route for datepicker components
Route::get('demo/datepicker', function () {
    return view('datepicker-demo');
})->name('demo.datepicker');

// Routes with {id} parameter - MUST come after specific routes
Route::get('client/{id}', [ClientMasterController::class, 'show'])->name('client.show')->middleware('auth');;
Route::get('client/{id}/edit', [ClientMasterController::class, 'edit'])->name('client.edit')->middleware('auth');;
Route::put('client/update/{id}', [ClientMasterController::class, 'update'])->name('client.update')->middleware('auth');;
Route::delete('client/delete/{id}', [ClientMasterController::class, 'destroy'])->name('client.delete')->middleware('auth');;

// Restore soft-deleted
Route::put('client/{id}/restore', [ClientMasterController::class, 'restore'])->name('client.restore')->middleware('auth');;

// Activate/Deactivate
Route::put('client/{id}/activate', [ClientMasterController::class, 'activate'])->name('client.activate')->middleware('auth');;
Route::put('client/{id}/deactivate', [ClientMasterController::class, 'deactivate'])->name('client.deactivate')->middleware('auth');;

// Duplicate client
Route::get('client/{id}/duplicate', [ClientMasterController::class, 'duplicate'])->name('client.duplicate')->middleware('auth');;

// Audit history
Route::get('client/{id}/audit', [ClientMasterController::class, 'audit'])->name('client.audit')->middleware('auth');;

// Check restore (for duplicate validation)
Route::get('/{id}/check-restore', [ClientMasterController::class, 'checkRestore'])->name('check-restore')->middleware('auth');;

// Address linking
Route::post('/{id}/addresses', [ClientMasterController::class, 'linkAddress'])->name('link-address')->middleware('auth');;
Route::delete('/{id}/addresses/{addressId}', [ClientMasterController::class, 'unlinkAddress'])->name('unlink-address')->middleware('auth');;

// CIMS Address management routes
Route::prefix('cimsaddresses')->name('cimsaddresses.')->middleware('auth')->group(function () {
    Route::get('/', [AddressController::class, 'index'])->name('index');

    // Search addresses (for AJAX dropdowns)
    Route::get('/search', [AddressController::class, 'search'])->name('search');

    // Create new address
    Route::get('/create', [AddressController::class, 'create'])->name('create');
    Route::post('/', [AddressController::class, 'store'])->name('store');

    // View single address
    Route::get('/{id}', [AddressController::class, 'show'])->name('show')->where('id', '[0-9]+');

    // Edit address
    Route::get('/{id}/edit', [AddressController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
    Route::put('/{id}', [AddressController::class, 'update'])->name('update')->where('id', '[0-9]+');

    // Delete address (soft delete)
    Route::delete('/{id}', [AddressController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');

    // Toggle active status
    Route::post('/{id}/toggle', [AddressController::class, 'toggle'])->name('toggle')->where('id', '[0-9]+');
    Route::get('/{id}/toggle', [AddressController::class, 'toggle'])->name('toggle.get')->where('id', '[0-9]+');

    // Check for duplicate before restore (AJAX)
    Route::get('/{id}/check-restore', [AddressController::class, 'checkRestoreDuplicate'])->name('checkRestore')->where('id', '[0-9]+');

    // Restore soft-deleted address
    Route::post('/{id}/restore', [AddressController::class, 'restore'])->name('restore')->where('id', '[0-9]+');

    // Permanently delete address
    Route::delete('/{id}/force', [AddressController::class, 'forceDelete'])->name('forceDelete')->where('id', '[0-9]+');


    Route::get('ajax/addresses', [ClientMasterController::class, 'getAddresses'])->name('ajax.addresses');
});

// CIMS DOCUMENT MANAGEMENT ROUTES
Route::prefix('cimsdocmanager')->name('cimsdocmanager.')->middleware('auth')->group(function () {
    Route::get('/', [DocManagerController::class, 'index'])->name('index');
    Route::get('/create', [DocManagerController::class, 'create'])->name('create');
    Route::post('/', [DocManagerController::class, 'store'])->name('store');
    Route::get('/{id}', [DocManagerController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [DocManagerController::class, 'edit'])->name('edit');
    Route::put('/{id}', [DocManagerController::class, 'update'])->name('update');
    Route::delete('/{id}', [DocManagerController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/download', [DocManagerController::class, 'download'])->name('download');
    Route::get('/types/{categoryId}', [DocManagerController::class, 'getTypesByCategory']);
    Route::get('/clients/search', [DocManagerController::class, 'searchClients']);

    Route::get('/view/{document}', [DocManagerController::class, 'view'])->name('view');
    Route::get('/view/client/{client_id}/{document}', [DocManagerController::class, 'view_client'])->name('view.client');

});

// Person Routes

Route::prefix('cimspersons')->name('cimspersons.')->middleware('auth')->group(function () {
    Route::get('/', [PersonController::class, 'index'])->name('index');
    Route::get('/search', [PersonController::class, 'search'])->name('search');
    Route::get('/create', [PersonController::class, 'create'])->name('create');
    Route::post('/', [PersonController::class, 'store'])->name('store');
    Route::get('/{id}', [PersonController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PersonController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PersonController::class, 'update'])->name('update');
    Route::delete('/{id}', [PersonController::class, 'destroy'])->name('destroy');

    // AJAX endpoints
    Route::post('/check-duplicate', [PersonController::class, 'checkDuplicate'])->name('checkDuplicate');
    Route::get('/banks/list', [PersonController::class, 'banks'])->name('banks');
    Route::get('/{id}/banks', [PersonController::class, 'personBanks'])->name('personBanks');
    Route::post('/{id}/banks', [PersonController::class, 'addBank'])->name('addBank');
    Route::delete('/{id}/banks/{bankId}', [PersonController::class, 'removeBank'])->name('removeBank');
    Route::get('/{id}/addresses', [PersonController::class, 'personAddresses'])->name('personAddresses');
    Route::post('/{id}/addresses', [PersonController::class, 'addAddress'])->name('addAddress');
    Route::delete('/{id}/addresses/{linkId}', [PersonController::class, 'removeAddress'])->name('removeAddress');
    Route::get('/addresses/search', [PersonController::class, 'searchAddresses'])->name('searchAddresses');

    Route::get('ajax/person/{id}', [PersonController::class, 'get_person'])->name('ajax.person.get');
});

Route::prefix('nsdcpersons')->name('nsdcpersons.')->middleware('auth')->group(function () {
    Route::get('/', [NsdcPersonController::class, 'index'])->name('index');
    Route::get('/search', [NsdcPersonController::class, 'search'])->name('search');
    Route::get('/create', [NsdcPersonController::class, 'create'])->name('create');
    Route::post('/', [NsdcPersonController::class, 'store'])->name('store');
    Route::get('/{id}', [NsdcPersonController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [NsdcPersonController::class, 'edit'])->name('edit');
    Route::put('/{id}', [NsdcPersonController::class, 'update'])->name('update');
    Route::delete('/{id}', [NsdcPersonController::class, 'destroy'])->name('destroy');

    // AJAX endpoints
    Route::post('/check-duplicate', [NsdcPersonController::class, 'checkDuplicate'])->name('checkDuplicate');
    Route::get('/banks/list', [NsdcPersonController::class, 'banks'])->name('banks');
    Route::get('/{id}/banks', [NsdcPersonController::class, 'personBanks'])->name('personBanks');
    Route::post('/{id}/banks', [NsdcPersonController::class, 'addBank'])->name('addBank');
    Route::delete('/{id}/banks/{bankId}', [NsdcPersonController::class, 'removeBank'])->name('removeBank');
    Route::get('/{id}/addresses', [NsdcPersonController::class, 'personAddresses'])->name('personAddresses');
    Route::post('/{id}/addresses', [NsdcPersonController::class, 'addAddress'])->name('addAddress');
    Route::delete('/{id}/addresses/{linkId}', [NsdcPersonController::class, 'removeAddress'])->name('removeAddress');
    Route::get('/addresses/search', [NsdcPersonController::class, 'searchAddresses'])->name('searchAddresses');
});

// SARS Representative Routes
Route::prefix('sarsrep')->name('sarsrep.')->middleware('auth')->group(function () {
    Route::get('/', [SarsRepController::class, 'index'])->name('index');

    // Create wizard - Step 1: Entity & Representative details
    Route::get('/create', [SarsRepController::class, 'create'])->name('create');
    Route::post('/', [SarsRepController::class, 'store'])->name('store');

    // Show request details
    Route::get('/{id}', [SarsRepController::class, 'show'])->name('show');

    // Edit request
    Route::get('/{id}/edit', [SarsRepController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SarsRepController::class, 'update'])->name('update');

    // Delete request
    Route::delete('/{id}', [SarsRepController::class, 'destroy'])->name('destroy');

    // Document upload (AJAX)
    Route::post('/{id}/upload', [SarsRepController::class, 'uploadDocument'])->name('upload');

    // Delete document (AJAX)
    Route::delete('/{id}/document/{docId}', [SarsRepController::class, 'deleteDocument'])->name('deleteDocument');

    // Generate documents (Mandate, Resolution, Cover Letter)
    Route::post('/{id}/generate/{type}', [SarsRepController::class, 'generateDocument'])->name('generate');

    // Generate final PDF bundle
    Route::post('/{id}/generate-bundle', [SarsRepController::class, 'generateBundle'])->name('generateBundle');

    // Download document
    Route::get('/{id}/download/{docId}', [SarsRepController::class, 'downloadDocument'])->name('download');

    // Update status (AJAX)
    Route::put('/{id}/status', [SarsRepController::class, 'updateStatus'])->name('updateStatus');

    // Audit log
    Route::get('/{id}/audit', [SarsRepController::class, 'audit'])->name('audit');
});

/*
|--------------------------------------------------------------------------
| Web Routes - CIMS Info Docs
|--------------------------------------------------------------------------
| Prefix: /cims/info-docs (set in RouteServiceProvider)
| Name: cimsinfodocs.
*/
Route::prefix('infodocs')->name('infodocs.')->middleware('auth')->group(function () {

    // Main view
    Route::get('/', [InfoDocsController::class, 'index'])->name('index');
    Route::get('/view/{client_id?}', [InfoDocsController::class, 'view'])->name('view');

    // Client status
    Route::post('/update-active-status', [InfoDocsController::class, 'updateActiveStatus'])->name('update-active-status');

    // Clients API
    Route::get('/get-clients', [InfoDocsController::class, 'getClients'])->name('get-clients');
    Route::post('/search-persons', [InfoDocsController::class, 'searchPersons'])->name('search-persons');

    // Directors
    Route::get('/get-directors/{client_id}', [InfoDocsController::class, 'getDirectors'])->name('get-directors');
    Route::get('/get-directors-info/{client_id}', [InfoDocsController::class, 'getDirectorsInfo'])->name('get-directors-info');
    Route::post('/get-directors-info/{client_id}', [InfoDocsController::class, 'getDirectorsInfo'])->name('get-directors-info-post');
    Route::get('/past-get-directors-info/{client_id}', [InfoDocsController::class, 'pastGetDirectorsInfo'])->name('past-get-directors-info');
    Route::post('/past-get-directors-info/{client_id}', [InfoDocsController::class, 'pastGetDirectorsInfo'])->name('past-get-directors-info-post');
    Route::get('/get-director-details/{id}', [InfoDocsController::class, 'getDirectorDetails'])->name('get-director-details');

    // Officers
    Route::get('/get-officers-info/{client_id}', [InfoDocsController::class, 'getOfficersInfo'])->name('get-officers-info');
    Route::post('/get-officers-info/{client_id}', [InfoDocsController::class, 'getOfficersInfo'])->name('get-officers-info-post');
    Route::get('/past-get-officers-info/{client_id}', [InfoDocsController::class, 'pastGetOfficersInfo'])->name('past-get-officers-info');
    Route::post('/past-get-officers-info/{client_id}', [InfoDocsController::class, 'pastGetOfficersInfo'])->name('past-get-officers-info-post');

    // Persons
    Route::get('/get-persons/{client_id}', [InfoDocsController::class, 'getPersons'])->name('get-persons');
    Route::get('/get-person-details/{id}', [InfoDocsController::class, 'getPersonDetails'])->name('get-person-details');

    // Director/Officer Appointments
    Route::post('/appoint-director', [InfoDocsController::class, 'appointDirector'])->name('appoint-director');
    Route::post('/appoint-officer', [InfoDocsController::class, 'appointOfficer'])->name('appoint-officer');
    Route::post('/terminate-director', [InfoDocsController::class, 'terminateDirector'])->name('terminate-director');
    Route::post('/terminate-officer', [InfoDocsController::class, 'terminateOfficer'])->name('terminate-officer');

    // Key Dates
    Route::get('/get-key-dates-info/{client_id}', [InfoDocsController::class, 'getKeyDatesInfo'])->name('get-key-dates-info');
    Route::post('/get-key-dates-info/{client_id}', [InfoDocsController::class, 'getKeyDatesInfo'])->name('get-key-dates-info-post');

    // Documents
    Route::get('/get-all-documents-info/{client_id}', [InfoDocsController::class, 'getAllDocumentsInfo'])->name('get-all-documents-info');
    Route::post('/get-all-documents-info/{client_id}', [InfoDocsController::class, 'getAllDocumentsInfo'])->name('get-all-documents-info-post');

    // Folder Management
    Route::get('/get-structure/{client_id}', [InfoDocsController::class, 'getStructure'])->name('get-structure');
    Route::post('/create-folder', [InfoDocsController::class, 'createFolder'])->name('create-folder');
    Route::get('/magic-folder/{client_id}', [InfoDocsController::class, 'magicFolder'])->name('magic-folder');
    Route::post('/upload-file', [InfoDocsController::class, 'uploadFile'])->name('upload-file');

    // Email
    Route::post('/send-document-mail', [InfoDocsController::class, 'sendDocumentMail'])->name('send-document-mail');

});



Route::prefix('sdcompliance')->name('sdcompliance.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [ComplianceCenterController::class, 'dashboard'])->name('dashboard');
    Route::post('/recalc', [ComplianceCenterController::class, 'recalculate'])->name('recalc');
    Route::post('/alerts/{alert}/resolve', [ComplianceCenterController::class, 'resolveAlert'])->name('alerts.resolve');
});

Route::resource('cipc-statuses', CipcCompanyStatusController::class)->middleware('auth');

// User Management
Route::resource('users', UserController::class)->middleware('auth');

// Route::get('/test', function () {
//     return view('test');
// })->name('test');

Route::get('/sign_pad', function () {
    return view('sign_pad');
})->name('sign_pad');

Route::get('/fix-storage', function () {
    // This command creates the symbolic link
    Artisan::call('storage:link');

    return 'Storage link created successfully!';
});

Route::get('/force-storage-link', function () {
    // 1. Get the absolute path to your 'storage/app/public' folder
    $targetFolder = storage_path('app/public');

    // 2. Get the absolute path to your 'public/storage' folder
    $linkFolder = public_path('storage');

    // 3. Check if the target actually exists
    if (! file_exists($targetFolder)) {
        return 'ERROR: The target folder does not exist: '.$targetFolder;
    }

    // 4. Check if a link already exists and delete it to be safe
    if (file_exists($linkFolder)) {
        return "ERROR: Please delete the 'storage' folder in your public directory manually via cPanel first.";
    }

    // 5. Create the symlink
    try {
        symlink($targetFolder, $linkFolder);

        return 'SUCCESS! <br>Linked: '.$targetFolder.'<br>To: '.$linkFolder;
    } catch (\Exception $e) {
        return 'FAILED: '.$e->getMessage();
    }
});
