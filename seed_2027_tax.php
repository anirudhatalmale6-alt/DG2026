<?php
/**
 * Seed SARS 2027 Tax Year Data (1 March 2026 - 28 February 2027)
 * Source: SARS official rates - https://www.sars.gov.za/tax-rates/income-tax/rates-of-tax-for-individuals/
 */

// Bootstrap Laravel
require_once '/usr/www/users/smartucbmh/application/vendor/autoload.php';
$app = require_once '/usr/www/users/smartucbmh/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$db = app('db');
$taxYear = '2027';
$now = date('Y-m-d H:i:s');

// Check if 2027 data already exists
$existing = $db->table('cims_payroll_tax_brackets')->where('tax_year', $taxYear)->count();
if ($existing > 0) {
    echo "2027 tax brackets already exist ($existing records). Skipping brackets.\n";
} else {
    // Tax Brackets - 2027 (1 March 2026 - 28 February 2027)
    $brackets = [
        ['tax_year' => $taxYear, 'min_amount' => 1,        'max_amount' => 245100,   'rate' => 18.00, 'base_tax' => 0,      'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ['tax_year' => $taxYear, 'min_amount' => 245101,   'max_amount' => 383100,   'rate' => 26.00, 'base_tax' => 44118,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ['tax_year' => $taxYear, 'min_amount' => 383101,   'max_amount' => 530200,   'rate' => 31.00, 'base_tax' => 79998,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ['tax_year' => $taxYear, 'min_amount' => 530201,   'max_amount' => 695800,   'rate' => 36.00, 'base_tax' => 125599, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ['tax_year' => $taxYear, 'min_amount' => 695801,   'max_amount' => 887000,   'rate' => 39.00, 'base_tax' => 185215, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ['tax_year' => $taxYear, 'min_amount' => 887001,   'max_amount' => 1878600,  'rate' => 41.00, 'base_tax' => 259783, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ['tax_year' => $taxYear, 'min_amount' => 1878601,  'max_amount' => 99999999, 'rate' => 45.00, 'base_tax' => 666339, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
    ];
    $db->table('cims_payroll_tax_brackets')->insert($brackets);
    echo "Inserted " . count($brackets) . " tax brackets for 2027.\n";
}

// Tax Rebates
$existingRebates = $db->table('cims_payroll_tax_rebates')->where('tax_year', $taxYear)->count();
if ($existingRebates > 0) {
    echo "2027 tax rebates already exist ($existingRebates records). Skipping rebates.\n";
} else {
    $rebates = [
        ['tax_year' => $taxYear, 'rebate_type' => 'primary',   'amount' => 17820, 'age_threshold' => null, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ['tax_year' => $taxYear, 'rebate_type' => 'secondary',  'amount' => 9765,  'age_threshold' => 65,   'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ['tax_year' => $taxYear, 'rebate_type' => 'tertiary',   'amount' => 3249,  'age_threshold' => 75,   'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
    ];
    $db->table('cims_payroll_tax_rebates')->insert($rebates);
    echo "Inserted " . count($rebates) . " tax rebates for 2027.\n";
}

// Tax Thresholds
$existingThresholds = $db->table('cims_payroll_tax_thresholds')->where('tax_year', $taxYear)->count();
if ($existingThresholds > 0) {
    echo "2027 tax thresholds already exist ($existingThresholds records). Skipping thresholds.\n";
} else {
    $thresholds = [
        ['tax_year' => $taxYear, 'age_group' => 'below_65',    'threshold_amount' => 99000,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ['tax_year' => $taxYear, 'age_group' => '65_to_74',    'threshold_amount' => 153250, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ['tax_year' => $taxYear, 'age_group' => '75_and_over',  'threshold_amount' => 171300, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
    ];
    $db->table('cims_payroll_tax_thresholds')->insert($thresholds);
    echo "Inserted " . count($thresholds) . " tax thresholds for 2027.\n";
}

echo "\nDone! 2027 SARS tax tables seeded successfully.\n";
