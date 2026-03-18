<?php
header('Content-Type: application/json');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

// Get active clients with PAYE numbers (for realistic data)
$clients = DB::table('client_master')
    ->where('is_active', 1)
    ->select('client_id', 'client_code', 'company_name', 'trading_name',
             'company_reg_number', 'vat_number', 'tax_number',
             'paye_number', 'sdl_number', 'uif_number')
    ->orderBy('client_id')
    ->limit(10)
    ->get()
    ->toArray();

// Get periods for tax years 2025 and 2026 (excluding the tax year summary rows)
$periods = DB::table('cims_document_periods')
    ->whereIn('tax_year', [2025, 2026])
    ->where('period_combo', 'NOT LIKE', '%-%') // Exclude "2025-00" type rows
    ->select('id', 'period_name', 'period_combo', 'tax_year')
    ->orderBy('tax_year')
    ->orderBy('period_combo')
    ->get()
    ->toArray();

$inserted = 0;
$details = [];
$now = date('Y-m-d H:i:s');

// Use a selection of clients (pick ones with PAYE numbers for realism, and a few without)
$selectedClients = [];
foreach ($clients as $c) {
    $selectedClients[] = $c;
}

// Seed random for reproducibility
mt_srand(42);

foreach ($selectedClients as $client) {
    // Each client gets data for some months across tax years
    foreach ($periods as $period) {
        // Skip some periods randomly (not every client has every month)
        if (mt_rand(1, 100) > 75) continue; // ~75% chance of having data for a period

        // Generate realistic financial values
        $payeBase = mt_rand(5000, 50000); // Base PAYE amount
        $payeLiability = round($payeBase + mt_rand(0, 5000) / 100, 2);
        $sdlLiability = round($payeLiability * 0.01 * mt_rand(80, 120), 2); // ~1% of payroll
        $uifLiability = round($payeLiability * 0.01 * mt_rand(80, 120), 2); // ~1% of payroll
        $payrollLiability = round($payeLiability + $sdlLiability + $uifLiability, 2);

        // ETI (Employment Tax Incentive) - only some companies
        $hasEti = mt_rand(1, 100) > 60; // 40% chance of ETI
        $etiBroughtForward = $hasEti ? round(mt_rand(100, 5000) + mt_rand(0, 99) / 100, 2) : 0;
        $etiCalculated = $hasEti ? round(mt_rand(200, 3000) + mt_rand(0, 99) / 100, 2) : 0;
        $etiUtilised = $hasEti ? round(min($etiCalculated, $payeLiability * 0.5), 2) : 0;
        $etiCarryForward = $hasEti ? round(max(0, $etiBroughtForward + $etiCalculated - $etiUtilised), 2) : 0;

        // Payable amounts (liability minus ETI where applicable)
        $payePayable = round(max(0, $payeLiability - $etiUtilised), 2);
        $sdlPayable = $sdlLiability;
        $uifPayable = $uifLiability;

        // Penalties (occasional)
        $hasPenalty = mt_rand(1, 100) > 85; // 15% chance
        $penalty = $hasPenalty ? round(mt_rand(100, 2000) + mt_rand(0, 99) / 100, 2) : 0;
        $interest = $hasPenalty ? round(mt_rand(50, 500) + mt_rand(0, 99) / 100, 2) : 0;
        $penaltyInterest = round($penalty + $interest, 2);

        $taxPayable = round($payePayable + $sdlPayable + $uifPayable + $penaltyInterest, 2);

        // PAYE number formatting
        $payeNum = $client->paye_number ?: ('7' . mt_rand(10, 99) . ' ' . mt_rand(100, 999) . ' ' . mt_rand(1000, 9999));

        // Payment reference
        $checkDigit = mt_rand(1, 99);
        $paymentPeriodCombo = $period->period_combo;
        $cleanPaye = preg_replace('/\s+/', '', $payeNum);
        $paymentRef = $cleanPaye . ' LC ' . $paymentPeriodCombo . ' ' . $checkDigit;

        $data = [
            'client_id' => $client->client_id,
            'client_code' => $client->client_code,
            'company_name' => $client->company_name,
            'trading_name' => $client->trading_name,
            'company_number' => $client->company_reg_number,
            'vat_number' => $client->vat_number,
            'income_tax_number' => $client->tax_number,
            'paye_number' => $client->paye_number,
            'sdl_number' => $client->sdl_number,
            'uif_number' => $client->uif_number,
            'period_id' => $period->id,
            'pay_period' => $period->period_name,
            'financial_year' => $period->tax_year,
            'period_combo' => $period->period_combo,
            'payment_period' => $period->period_combo,
            'paye_liability' => $payeLiability,
            'sdl_liability' => $sdlLiability,
            'uif_liability' => $uifLiability,
            'payroll_liability' => $payrollLiability,
            'eti_indicator' => $hasEti ? 'Y' : 'N',
            'eti_brought_forward' => $etiBroughtForward,
            'eti_calculated' => $etiCalculated,
            'eti_utilised' => $etiUtilised,
            'eti_carry_forward' => $etiCarryForward,
            'paye_payable' => $payePayable,
            'sdl_payable' => $sdlPayable,
            'uif_payable' => $uifPayable,
            'penalty' => $penalty,
            'interest' => $interest,
            'penalty_interest' => $penaltyInterest,
            'tax_payable' => $taxPayable,
            'payment_reference' => $paymentRef,
            'check_digit' => str_pad($checkDigit, 2, '0', STR_PAD_LEFT),
            'payment_reference_number' => $paymentRef,
            'status' => 1,
            'user_id' => 1,
            'notes' => 'Sample data for pivot table testing',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('cims_emp201_declarations')->insert($data);
        $inserted++;

        $details[] = [
            'client' => $client->client_code,
            'year' => $period->tax_year,
            'combo' => $period->period_combo,
            'period' => $period->period_name,
            'tax_payable' => $taxPayable,
        ];
    }
}

// Summary
$totalByClient = [];
$totalByYear = [];
foreach ($details as $d) {
    $totalByClient[$d['client']] = ($totalByClient[$d['client']] ?? 0) + 1;
    $totalByYear[$d['year']] = ($totalByYear[$d['year']] ?? 0) + 1;
}

echo json_encode([
    'success' => true,
    'total_inserted' => $inserted,
    'by_client' => $totalByClient,
    'by_year' => $totalByYear,
    'sample_records' => array_slice($details, 0, 10),
], JSON_PRETTY_PRINT);
