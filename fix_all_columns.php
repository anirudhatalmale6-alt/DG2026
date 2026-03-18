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
use Illuminate\Database\Schema\Blueprint;

echo "<pre>\n=== ADDING MISSING COLUMNS TO ALL TABLES ===\n\n";

// -------------------------------------------------------
// 1. client_master - Add missing columns
// -------------------------------------------------------
echo "=== client_master ===\n";
$clientMasterCols = [
    'number_of_directors' => ['type' => 'integer', 'nullable' => true],
    'number_of_shares' => ['type' => 'integer', 'nullable' => true],
    'share_type_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'vat_cycle_id' => ['type' => 'integer', 'nullable' => true],
    'vat_cycle_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'last_vat_return' => ['type' => 'date', 'nullable' => true],
    'email_admin' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'direct' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'sign_text' => ['type' => 'longText', 'nullable' => true],
    'income_tax_notice_registration_uploaded' => ['type' => 'tinyInteger', 'default' => 0],
    'payroll_notice_registration_uploaded' => ['type' => 'tinyInteger', 'default' => 0],
    'vat_registration_uploaded' => ['type' => 'tinyInteger', 'default' => 0],
    'sars_representative_uploaded' => ['type' => 'tinyInteger', 'default' => 0],
    'confirmation_of_banking_uplaoded' => ['type' => 'tinyInteger', 'default' => 0],
    'income_tax_registration' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'payroll_registration' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'vat_registration' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'cor_14_3_certificate' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'proof_of_bank_1' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'proof_of_bank_2' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'proof_of_bank_3' => ['type' => 'string', 'length' => 500, 'nullable' => true],
];

addColumns('client_master', $clientMasterCols);

// -------------------------------------------------------
// 2. client_master_addresses - Add missing columns
// -------------------------------------------------------
echo "\n=== client_master_addresses ===\n";
$addressCols = [
    'address_type_id' => ['type' => 'integer', 'nullable' => true],
    'address_type_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'unit_number' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'complex_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'street_number' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'street_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'suburb' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'city' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'postal_code' => ['type' => 'string', 'length' => 20, 'nullable' => true],
    'province' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'municipality' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'ward' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'country' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'long_address' => ['type' => 'text', 'nullable' => true],
    'google_address' => ['type' => 'text', 'nullable' => true],
    'latitude' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'longitude' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'map_url' => ['type' => 'text', 'nullable' => true],
    'is_checked' => ['type' => 'boolean', 'default' => false],
];

addColumns('client_master_addresses', $addressCols);

// -------------------------------------------------------
// 3. client_master_directors - Add missing columns
// -------------------------------------------------------
echo "\n=== client_master_directors ===\n";
$directorCols = [
    'number_of_director_shares' => ['type' => 'integer', 'nullable' => true, 'default' => 0],
    'director_type_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'director_status_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'date_engaged' => ['type' => 'date', 'nullable' => true],
    'date_resigned' => ['type' => 'date', 'nullable' => true],
    'citizenship' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'identity_type' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'identity_number' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'gender' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'date_of_birth' => ['type' => 'date', 'nullable' => true],
    'date_of_issue' => ['type' => 'date', 'nullable' => true],
    'person_status' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'date_of_death' => ['type' => 'date', 'nullable' => true],
    'ethnic_group' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'disability' => ['type' => 'string', 'length' => 10, 'nullable' => true, 'default' => '0'],
    'passport_number' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'passport_expiry' => ['type' => 'date', 'nullable' => true],
    'country' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'country_code' => ['type' => 'string', 'length' => 10, 'nullable' => true],
    'nationality' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'title' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'initials' => ['type' => 'string', 'length' => 20, 'nullable' => true],
    'surname' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'firstname' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'middlename' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'known_as' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'tax_number' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'mobile_phone' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'whatsapp_number' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'office_phone' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'other_phone' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'email' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'accounts_email' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'marital_status' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'marital_status_date' => ['type' => 'date', 'nullable' => true],
    // Spouse fields
    'sp_citizenship' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'sp_identity_type' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'sp_identity_number' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'sp_date_of_birth' => ['type' => 'date', 'nullable' => true],
    'sp_date_of_issue' => ['type' => 'date', 'nullable' => true],
    'sp_person_status' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'sp_gender' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'sp_ethnic_group' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'sp_disability' => ['type' => 'string', 'length' => 10, 'nullable' => true],
    'sp_title' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'sp_initials' => ['type' => 'string', 'length' => 20, 'nullable' => true],
    'sp_tax_number' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'sp_firstname' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'sp_middlename' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'sp_surname' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'sp_known_as' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'sp_mobile_phone' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'sp_whatsapp_number' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'sp_office_phone' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'sp_other_phone' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'sp_email' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'sp_accounts_email' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    // Address fields
    'complex_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'address_line' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'address_line_2' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'suburb' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'city' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'postal_code' => ['type' => 'string', 'length' => 20, 'nullable' => true],
    'province' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'address_country' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'latitude' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'longitude' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    // Banking fields
    'bank_account_holder' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'bank_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'bank_branch' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'bank_account_number' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'bank_account_type' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'bank_swift_code' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'bank_account_status' => ['type' => 'string', 'length' => 100, 'nullable' => true],
    'bank_date_opened' => ['type' => 'date', 'nullable' => true],
    'notes' => ['type' => 'text', 'nullable' => true],
    'profile_photo' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'id_front_image' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'id_back_image' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'green_book_image' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'update_image' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'passport_image' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'poa_image' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'banking_image' => ['type' => 'string', 'length' => 500, 'nullable' => true],
    'signature_image' => ['type' => 'longText', 'nullable' => true],
    'created_by' => ['type' => 'string', 'length' => 100, 'nullable' => true],
];

addColumns('client_master_directors', $directorCols);

// -------------------------------------------------------
// 4. client_master_banks - Add missing columns
// -------------------------------------------------------
echo "\n=== client_master_banks ===\n";
$bankCols = [
    'bank_id' => ['type' => 'integer', 'nullable' => true],
    'bank_account_type_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'bank_account_status_id' => ['type' => 'integer', 'nullable' => true],
    'bank_account_status_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'bank_statement_frequency_id' => ['type' => 'integer', 'nullable' => true],
    'bank_statement_frequency_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'bank_statement_cut_off_date' => ['type' => 'date', 'nullable' => true],
    'bank_branch_name' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'bank_swift_code' => ['type' => 'string', 'length' => 50, 'nullable' => true],
    'bank_account_date_opened' => ['type' => 'date', 'nullable' => true],
    'is_default' => ['type' => 'boolean', 'default' => false],
    'document_id' => ['type' => 'integer', 'nullable' => true],
];

addColumns('client_master_banks', $bankCols);

// -------------------------------------------------------
// Helper function
// -------------------------------------------------------
function addColumns($table, $columns) {
    if (!Schema::hasTable($table)) {
        echo "  TABLE MISSING: $table\n";
        return;
    }

    $added = 0;
    $skipped = 0;

    foreach ($columns as $colName => $colDef) {
        if (Schema::hasColumn($table, $colName)) {
            $skipped++;
            continue;
        }

        Schema::table($table, function (Blueprint $t) use ($colName, $colDef) {
            $type = $colDef['type'];
            $col = null;

            switch ($type) {
                case 'string':
                    $col = $t->string($colName, $colDef['length'] ?? 255);
                    break;
                case 'integer':
                    $col = $t->integer($colName);
                    break;
                case 'tinyInteger':
                    $col = $t->tinyInteger($colName);
                    break;
                case 'boolean':
                    $col = $t->boolean($colName);
                    break;
                case 'date':
                    $col = $t->date($colName);
                    break;
                case 'text':
                    $col = $t->text($colName);
                    break;
                case 'longText':
                    $col = $t->longText($colName);
                    break;
            }

            if ($col) {
                if (isset($colDef['nullable']) && $colDef['nullable']) {
                    $col->nullable();
                }
                if (isset($colDef['default'])) {
                    $col->default($colDef['default']);
                }
            }
        });

        $added++;
        echo "  [ADD] $colName ({$colDef['type']})\n";
    }

    echo "  Summary: added=$added, skipped=$skipped (already exist)\n";
}

echo "\n=== ALL COLUMNS MIGRATION COMPLETE ===\n</pre>";
