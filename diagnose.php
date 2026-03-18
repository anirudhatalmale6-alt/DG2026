<?php
$basePaths = [__DIR__ . '/../application', __DIR__ . '/../../application', __DIR__ . '/..'];
$bootstrapped = false;
foreach ($basePaths as $base) {
    if (file_exists($base . '/bootstrap/app.php')) {
        if (file_exists($base . '/bootstrap/autoload.php')) require $base . '/bootstrap/autoload.php';
        elseif (file_exists($base . '/vendor/autoload.php')) require $base . '/vendor/autoload.php';
        $app = require_once $base . '/bootstrap/app.php';
        $bootstrapped = true;
        break;
    }
}
if (!$bootstrapped) die("Could not find Laravel bootstrap.");
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

echo "<pre>\n=== FULL DIAGNOSTIC FOR ClientMasterNew ===\n\n";

// 1. Check all tables exist and have data
echo "=== DATABASE TABLES ===\n";
$tables = [
    'client_master', 'client_master_addresses', 'client_master_audit',
    'client_master_banks', 'client_master_directors', 'client_master_documents',
    'client_master_lookups', 'client_master_bank_documents_pivot',
    'company_types', 'client_titles', 'client_positions', 'share_types',
    'cims_vat_cycles', 'cims_director_types', 'cims_director_status',
    'cims_bank_names', 'cims_bank_account_types', 'cims_bank_statement_frequency',
    'cims_address_types', 'cims_addresses', 'cims_persons', 'cims_documents', 'cims_document_types',
];

foreach ($tables as $t) {
    if (Schema::hasTable($t)) {
        $count = DB::table($t)->count();
        $cols = Schema::getColumnListing($t);
        echo "  OK: $t ($count rows) [" . implode(', ', $cols) . "]\n";
    } else {
        echo "  MISSING: $t\n";
    }
}

// 2. Check all routes are registered
echo "\n=== ROUTES ===\n";
$routeNames = [
    'client.index', 'client.create', 'client.store', 'client.show', 'client.edit',
    'client.update', 'client.delete', 'client.restore', 'client.activate', 'client.deactivate',
    'client.duplicate', 'client.audit',
    'client.ajax.check-company-name', 'client.ajax.generate-code', 'client.ajax.get-company-type',
    'client.ajax.addresses', 'client.ajax.directors.update', 'client.ajax.client.get', 'client.ajax.director.get',
    'client.clear.cache',
];

foreach ($routeNames as $name) {
    $exists = Route::has($name);
    echo "  " . ($exists ? "OK" : "MISSING") . ": $name\n";
}

// 3. Check all model classes can be loaded
echo "\n=== MODELS ===\n";
$models = [
    'Modules\ClientMasterNew\Models\ClientMaster',
    'Modules\ClientMasterNew\Models\ClientMasterAddress',
    'Modules\ClientMasterNew\Models\ClientMasterAudit',
    'Modules\ClientMasterNew\Models\ClientMasterBank',
    'Modules\ClientMasterNew\Models\ClientMasterDirector',
    'Modules\ClientMasterNew\Models\ClientMasterDocument',
    'Modules\ClientMasterNew\Models\ClientMasterLookup',
    'Modules\ClientMasterNew\Models\Address',
    'Modules\ClientMasterNew\Models\CompanyType',
    'Modules\ClientMasterNew\Models\Document',
    'Modules\ClientMasterNew\Models\Person',
    'Modules\ClientMasterNew\Models\CimsAddressType',
    'Modules\ClientMasterNew\Models\CimsBankAccountType',
    'Modules\ClientMasterNew\Models\CimsDirectorStatus',
    'Modules\ClientMasterNew\Models\CimsDirectorType',
    'Modules\ClientMasterNew\Models\ClientPosition',
    'Modules\ClientMasterNew\Models\ClientTitle',
    'Modules\ClientMasterNew\Models\DocumentType',
    'Modules\ClientMasterNew\Models\RefBank',
    'Modules\ClientMasterNew\Models\ShareType',
    'Modules\ClientMasterNew\Models\CimsVatCycle',
];

foreach ($models as $model) {
    try {
        if (class_exists($model)) {
            echo "  OK: $model\n";
        } else {
            echo "  MISSING CLASS: $model\n";
        }
    } catch (Exception $e) {
        echo "  ERROR: $model - " . $e->getMessage() . "\n";
    }
}

// 4. Check view files exist
echo "\n=== VIEW FILES ===\n";
$viewBase = base_path('Modules/ClientMasterNew/Resources/views/clientmaster/');
$views = ['clientmaster_create', 'index', 'show', 'audit', '_card', '_deleted_card',
           'bank_details_js', 'director_js', 'address_details_js'];
foreach ($views as $v) {
    $path = $viewBase . $v . '.blade.php';
    echo "  " . (file_exists($path) ? "OK" : "MISSING") . ": $v.blade.php\n";
}

// 5. Check component files exist
echo "\n=== COMPONENT FILES ===\n";
$compBase = base_path('resources/views/components/');
$components = ['save-button', 'datepicker-previous', 'datepicker-all', 'datepicker-max',
               'datepicker-range', 'primary-breadcrumb', 'dropdown-with-tooltip', 'tooltip-selectbox',
               'card', 'edit-button', 'delete-button', 'cancel-button', 'close-button',
               'update-button', 'pink-button', 'print-button', 'signature-pad',
               'signature-pad-javascript', 'password-asterisk'];
foreach ($components as $c) {
    $path = $compBase . $c . '.blade.php';
    echo "  " . (file_exists($path) ? "OK" : "MISSING") . ": $c.blade.php\n";
}

// 6. Check layout and partials
echo "\n=== LAYOUT & PARTIALS ===\n";
$layoutPath = base_path('resources/views/layouts/default.blade.php');
echo "  " . (file_exists($layoutPath) ? "OK" : "MISSING") . ": layouts/default.blade.php\n";

$partials = ['cims_master_header', 'cims_master_menu', 'cims_master_footer'];
foreach ($partials as $p) {
    $path = base_path('Modules/CIMSCore/Resources/views/partials/' . $p . '.blade.php');
    echo "  " . (file_exists($path) ? "OK" : "MISSING") . ": CIMSCore partials/$p.blade.php\n";
}

// 7. Check JS/CSS assets
echo "\n=== JS/CSS ASSETS ===\n";
$publicBase = public_path();
$assets = [
    'smartdash/vendor/global/global.min.js',
    'smartdash/vendor/moment/moment.min.js',
    'smartdash/vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js',
    'smartdash/vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css',
    'smartdash/vendor/sweetalert2/sweetalert2.min.js',
    'smartdash/vendor/toastr/js/toastr.min.js',
    'smartdash/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
];
foreach ($assets as $a) {
    $path = $publicBase . '/' . $a;
    echo "  " . (file_exists($path) ? "OK" : "MISSING") . ": $a\n";
}

// 8. Check bank logos
echo "\n=== BANK LOGOS ===\n";
$logos = DB::table('cims_bank_names')->pluck('bank_logo');
foreach ($logos as $logo) {
    $path = $publicBase . $logo;
    echo "  " . (file_exists($path) ? "OK" : "MISSING") . ": $logo\n";
}

// 9. Check helper function
echo "\n=== HELPERS ===\n";
echo "  formatDateValue: " . (function_exists('formatDateValue') ? "OK" : "MISSING") . "\n";

// 10. Try simulating the create method data
echo "\n=== SIMULATE CREATE METHOD ===\n";
try {
    $lookups = \Modules\ClientMasterNew\Models\ClientMasterLookup::getByCategory('title');
    echo "  Lookups: OK (titles: " . count($lookups) . " rows)\n";
} catch (Exception $e) {
    echo "  Lookups ERROR: " . $e->getMessage() . "\n";
}

try {
    $code = \Modules\ClientMasterNew\Models\ClientMaster::generateClientCode();
    echo "  generateClientCode: $code\n";
} catch (Exception $e) {
    echo "  generateClientCode ERROR: " . $e->getMessage() . "\n";
}

try {
    $addresses = \Modules\ClientMasterNew\Models\Address::active()->latest()->get();
    echo "  Addresses: " . count($addresses) . " rows\n";
} catch (Exception $e) {
    echo "  Addresses ERROR: " . $e->getMessage() . "\n";
}

try {
    $addrTypes = \Modules\ClientMasterNew\Models\CimsAddressType::active()->latest()->get();
    echo "  Address Types: " . count($addrTypes) . " rows\n";
} catch (Exception $e) {
    echo "  Address Types ERROR: " . $e->getMessage() . "\n";
}

try {
    $banks = \Modules\ClientMasterNew\Models\RefBank::where('is_active', 1)->get();
    echo "  Banks: " . count($banks) . " rows\n";
} catch (Exception $e) {
    echo "  Banks ERROR: " . $e->getMessage() . "\n";
}

try {
    $acctTypes = \Modules\ClientMasterNew\Models\CimsBankAccountType::latest()->get();
    echo "  Account Types: " . count($acctTypes) . " rows\n";
} catch (Exception $e) {
    echo "  Account Types ERROR: " . $e->getMessage() . "\n";
}

try {
    $companyTypes = \Modules\ClientMasterNew\Models\CompanyType::getActive();
    echo "  Company Types: " . count($companyTypes) . " rows\n";
} catch (Exception $e) {
    echo "  Company Types ERROR: " . $e->getMessage() . "\n";
}

try {
    $persons = \Modules\ClientMasterNew\Models\Person::where('is_active', 1)->get();
    echo "  Persons: " . count($persons) . " rows\n";
} catch (Exception $e) {
    echo "  Persons ERROR: " . $e->getMessage() . "\n";
}

try {
    $dirTypes = \Modules\ClientMasterNew\Models\CimsDirectorType::where('is_active', true)->get();
    echo "  Director Types: " . count($dirTypes) . " rows\n";
} catch (Exception $e) {
    echo "  Director Types ERROR: " . $e->getMessage() . "\n";
}

try {
    $dirStatuses = \Modules\ClientMasterNew\Models\CimsDirectorStatus::where('is_active', true)->get();
    echo "  Director Statuses: " . count($dirStatuses) . " rows\n";
} catch (Exception $e) {
    echo "  Director Statuses ERROR: " . $e->getMessage() . "\n";
}

try {
    $shareTypes = \Modules\ClientMasterNew\Models\ShareType::latest()->get();
    echo "  Share Types: " . count($shareTypes) . " rows\n";
} catch (Exception $e) {
    echo "  Share Types ERROR: " . $e->getMessage() . "\n";
}

try {
    $vatCycles = \Modules\ClientMasterNew\Models\CimsVatCycle::latest()->get();
    echo "  VAT Cycles: " . count($vatCycles) . " rows\n";
} catch (Exception $e) {
    echo "  VAT Cycles ERROR: " . $e->getMessage() . "\n";
}

try {
    $clientTitles = \Modules\ClientMasterNew\Models\ClientTitle::latest()->get();
    echo "  Client Titles: " . count($clientTitles) . " rows\n";
} catch (Exception $e) {
    echo "  Client Titles ERROR: " . $e->getMessage() . "\n";
}

try {
    $clientPositions = \Modules\ClientMasterNew\Models\ClientPosition::latest()->get();
    echo "  Client Positions: " . count($clientPositions) . " rows\n";
} catch (Exception $e) {
    echo "  Client Positions ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== DIAGNOSTIC COMPLETE - DELETE THIS FILE ===\n</pre>";
