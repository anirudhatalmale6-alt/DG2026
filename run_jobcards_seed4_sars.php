<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

function seedJobType($data, $order) {
    $typeId = DB::table('job_card_types')->insertGetId([
        'name' => $data['name'],
        'description' => $data['description'],
        'submission_to' => $data['submission_to'],
        'display_order' => $order,
        'is_active' => 1,
        'created_by' => 1, 'updated_by' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);

    foreach ($data['steps'] as $i => $s) {
        DB::table('job_card_type_steps')->insert(array_merge($s, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    foreach ($data['fields'] as $i => $f) {
        DB::table('job_card_type_fields')->insert(array_merge($f, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    foreach ($data['docs'] as $i => $d) {
        DB::table('job_card_type_documents')->insert(array_merge($d, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    return $data['name'];
}

try {
    $maxOrder = DB::table('job_card_types')->max('display_order') ?? 0;
    $order = $maxOrder + 1;
    $seeded = [];

    // =====================================================
    // 1. Turnover Tax Registration & Return
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Turnover Tax Registration & Return',
        'description' => 'Register for and/or submit Turnover Tax return for micro businesses (turnover below R1m)',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify client qualifies for Turnover Tax (turnover < R1m)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm client is not a personal service provider or professional services', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect income records for the period', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Register for Turnover Tax on eFiling (if new)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Calculate turnover tax per sliding scale', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete TT01/TT02/TT03 return on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm payment (if applicable)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission receipt', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Business Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Income Records / Bank Statements', 'is_required' => 1],
            ['document_label' => 'Turnover Tax Return Receipt', 'is_required' => 1],
            ['document_label' => 'Proof of Payment', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 2. SARS eFiling Registration / Profile Setup
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'SARS eFiling Registration / Profile Setup',
        'description' => 'Register new client on SARS eFiling and set up tax practitioner access',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Collect client ID / company registration number', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Verify client tax number with SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Register client on eFiling (if not registered)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Activate all relevant tax types (IT, VAT, PAYE, etc.)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Link client portfolio to tax practitioner profile', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Upload Power of Attorney (if required)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Verify all tax types are accessible', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Update client master with eFiling details', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company / Taxpayer Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 0],
            ['field_name' => 'director_id_number', 'field_label' => 'ID Number', 'is_required' => 0],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_mobile', 'field_label' => 'Mobile', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Client ID Copy / COR Certificate', 'is_required' => 1],
            ['document_label' => 'Power of Attorney (if required)', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 3. Income Tax Deregistration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Income Tax Deregistration',
        'description' => 'Deregister company or individual from SARS income tax',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify all outstanding income tax returns are filed', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify no outstanding SARS debt on income tax', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm reason for deregistration', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete RAV01 update on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit deregistration request to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor deregistration status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download deregistration confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company / Taxpayer Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 0],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Final Income Tax Return', 'is_required' => 1],
            ['document_label' => 'SARS Deregistration Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 4. PAYE / UIF / SDL Deregistration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'PAYE / UIF / SDL Deregistration',
        'description' => 'Deregister employer from PAYE, UIF and SDL with SARS',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Confirm no employees remain on payroll', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit final EMP201 return', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit final EMP501 reconciliation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Issue final IRP5/IT3(a) certificates', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify no outstanding PAYE debt', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete RAV01 to deregister PAYE on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit deregistration to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Deregister on uFiling (UIF) if applicable', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Monitor deregistration status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download deregistration confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 1],
            ['field_name' => 'uif_number', 'field_label' => 'UIF Number', 'is_required' => 0],
            ['field_name' => 'sdl_number', 'field_label' => 'SDL Number', 'is_required' => 0],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Final EMP201 Return', 'is_required' => 1],
            ['document_label' => 'Final EMP501 Reconciliation', 'is_required' => 1],
            ['document_label' => 'Final IRP5/IT3(a) Certificates', 'is_required' => 1],
            ['document_label' => 'SARS PAYE Deregistration Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 5. SARS Request for Correction / Reduced Assessment
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'SARS Request for Correction',
        'description' => 'Request for reduced assessment or correction of a previously submitted return',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Identify error in original return', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Determine if correction or reduced assessment applies', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect supporting documents for correction', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare motivation letter (for reduced assessment)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Submit revised return on eFiling (if correction)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Submit Request for Reduced Assessment on eFiling (if RRA)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Upload supporting documents', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download revised assessment', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Advise client of outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company / Taxpayer Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Original Assessment / Return', 'is_required' => 1],
            ['document_label' => 'Supporting Documents for Correction', 'is_required' => 1],
            ['document_label' => 'Motivation Letter (if RRA)', 'is_required' => 0],
            ['document_label' => 'Revised Assessment', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 6. SARS Administrative Penalty Remission
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'SARS Administrative Penalty Remission',
        'description' => 'Request for remission of administrative non-compliance penalties under s218',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Review penalty notice and determine penalty type', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Verify outstanding return has been submitted', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Determine if first-time offender or reasonable grounds exist', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare Request for Remission (RFR) motivation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect supporting evidence', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Submit RFR via eFiling (SARS Online Query system)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Upload supporting documents', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor RFR outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download SARS response letter', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'If declined, consider objection (ADR1)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Advise client of outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company / Taxpayer Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'SARS Penalty Notice', 'is_required' => 1],
            ['document_label' => 'RFR Motivation Letter', 'is_required' => 1],
            ['document_label' => 'Supporting Evidence', 'is_required' => 1],
            ['document_label' => 'SARS Response Letter', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 7. Withholding Tax on Interest (WTI)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Withholding Tax on Interest (WTI)',
        'description' => 'Withholding tax declaration and payment on interest paid to non-residents (s50B)',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Confirm non-resident status of interest recipient', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Check if DTA (Double Tax Agreement) reduction applies', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate withholding tax at 15% (or DTA rate)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete WTI01 declaration on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Ensure payment by last day of following month', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission receipt', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Issue withholding tax certificate to payee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Non-Resident Status Confirmation', 'is_required' => 1],
            ['document_label' => 'DTA Certificate (if applicable)', 'is_required' => 0],
            ['document_label' => 'WTI01 Submission Receipt', 'is_required' => 1],
            ['document_label' => 'Proof of Payment', 'is_required' => 1],
            ['document_label' => 'Withholding Tax Certificate Issued', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 8. Withholding Tax on Royalties (WTR)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Withholding Tax on Royalties (WTR)',
        'description' => 'Withholding tax declaration on royalties paid to non-residents (s49B)',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Confirm non-resident status of royalty recipient', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Check if DTA reduction applies', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate withholding tax at 15% (or DTA rate)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete WTR01 declaration on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Ensure payment by last day of following month', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission receipt', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Issue withholding tax certificate to payee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Non-Resident Status Confirmation', 'is_required' => 1],
            ['document_label' => 'DTA Certificate (if applicable)', 'is_required' => 0],
            ['document_label' => 'Royalty Agreement', 'is_required' => 1],
            ['document_label' => 'WTR01 Submission Receipt', 'is_required' => 1],
            ['document_label' => 'Proof of Payment', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 9. Withholding Tax on Service Fees (WTS)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Withholding Tax on Service Fees (WTS)',
        'description' => 'Withholding tax on management/technical/consulting fees paid to non-residents (s51A)',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Confirm non-resident status of service provider', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify service type falls under s51A', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Check if DTA reduction applies', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate withholding tax at 15% (or DTA rate)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete WTS01 declaration on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Ensure payment by last day of following month', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission receipt', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Issue withholding tax certificate to payee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Non-Resident Status Confirmation', 'is_required' => 1],
            ['document_label' => 'DTA Certificate (if applicable)', 'is_required' => 0],
            ['document_label' => 'Service Agreement / Invoice', 'is_required' => 1],
            ['document_label' => 'WTS01 Submission Receipt', 'is_required' => 1],
            ['document_label' => 'Proof of Payment', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 10. Employment Tax Incentive (ETI) Claim
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Employment Tax Incentive (ETI) Claim',
        'description' => 'Claim Employment Tax Incentive for qualifying young employees (18-29 years)',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify employer qualifies for ETI (registered for PAYE, not in national/provincial/local sphere)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Identify qualifying employees (18-29, earning R2k-R6.5k/month)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify employees have valid SA ID numbers', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate ETI amount per qualifying employee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Include ETI claim in EMP201 submission', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify ETI offset against PAYE liability', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit EMP201 with ETI claim to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission receipt', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Record ETI in EMP501 reconciliation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Qualifying Employee List', 'is_required' => 1],
            ['document_label' => 'Employee ID Copies', 'is_required' => 1],
            ['document_label' => 'ETI Calculation Schedule', 'is_required' => 1],
            ['document_label' => 'EMP201 Submission Receipt', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 11. Section 18A PBO Application
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Section 18A PBO Application',
        'description' => 'Apply for Public Benefit Organisation (PBO) approval and s18A tax-deductible donation status',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify NPC is registered with CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify qualifying public benefit activity (9th Schedule)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review MOI / founding document for PBO compliance', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare PBO application (EI1 form)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare s18A application (EI2 form)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Collect supporting documents', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Submit to SARS Tax Exemption Unit', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor application status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download PBO approval letter', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Download s18A approval (if applied)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Update client records with PBO number', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'NPC / Organisation Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'NPC COR Certificate', 'is_required' => 1],
            ['document_label' => 'MOI / Founding Document', 'is_required' => 1],
            ['document_label' => 'EI1 PBO Application Form', 'is_required' => 1],
            ['document_label' => 'EI2 s18A Application Form', 'is_required' => 0],
            ['document_label' => 'PBO Approval Letter', 'is_required' => 1],
            ['document_label' => 's18A Approval Letter', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 12. Tax Compliance Status (TCS) - Foreign Investment / Emigration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Tax Compliance Status (TCS) - Foreign Investment',
        'description' => 'Apply for Tax Compliance Status pin for foreign investment allowance or financial emigration',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify all tax returns are up to date', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify no outstanding SARS debt', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Determine TCS type (FIA / Emigration / Tender)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect foreign investment / emigration details', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Apply for TCS on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete declaration of assets and liabilities', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Respond to SARS queries (if any)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Download TCS pin / approval', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send TCS pin to client / authorised dealer', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Surname', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'ID Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'ID Copy', 'is_required' => 1],
            ['document_label' => 'Foreign Investment Details / Bank Letters', 'is_required' => 1],
            ['document_label' => 'Declaration of Assets & Liabilities', 'is_required' => 1],
            ['document_label' => 'TCS Pin / Approval Letter', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 13. Foreign Employment Income Exemption (s10(1)(o)(ii))
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Foreign Employment Income Exemption s10(1)(o)(ii)',
        'description' => 'Claim foreign employment income exemption for SA residents working abroad (183/60 day rule)',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify SA tax residency status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify employment contract is with foreign employer', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate days outside SA (must exceed 183 in 12-month period)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify 60 consecutive days outside SA', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect passport stamps / travel records', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect employment contract', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Calculate exempt amount (first R1.25m exempt, balance taxable)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete ITR12 with s10(1)(o)(ii) exemption', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Respond to SARS verification (if selected)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Download assessment', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send assessment to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Surname', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'ID / Passport Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Passport Copy (all pages with stamps)', 'is_required' => 1],
            ['document_label' => 'Foreign Employment Contract', 'is_required' => 1],
            ['document_label' => 'Travel Log / Days Calculation', 'is_required' => 1],
            ['document_label' => 'Foreign Payslips / Income Proof', 'is_required' => 1],
            ['document_label' => 'SARS Notice of Assessment', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 14. IT3 Third Party Data Submission
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'IT3 Third Party Data Submission',
        'description' => 'Submit IT3 third party data certificates to SARS (IT3(b), IT3(c), IT3(s))',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Determine which IT3 types apply (IT3(b) interest, IT3(c) contract income, IT3(s) share transactions)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect all relevant transaction data for the period', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Validate recipient tax numbers', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare IT3 data file in SARS format', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Upload IT3 file on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify submission was accepted (no errors)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Correct and resubmit if errors found', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Download submission confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Issue IT3 certificates to recipients', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Transaction Data / Source Records', 'is_required' => 1],
            ['document_label' => 'IT3 Data File', 'is_required' => 1],
            ['document_label' => 'SARS Submission Confirmation', 'is_required' => 1],
            ['document_label' => 'IT3 Certificates Issued', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 15. ITR14SD - Supplementary Declaration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'ITR14SD - Supplementary Declaration',
        'description' => 'Submit ITR14SD supplementary declaration for transfer pricing, thin capitalisation, controlled foreign companies',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Determine if ITR14SD is required (connected person transactions)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Identify all related party / connected person transactions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect intercompany agreements and invoices', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Review transfer pricing policy', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Check thin capitalisation provisions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review CFC implications (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Complete ITR14SD on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS (with or after ITR14)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Intercompany Agreements', 'is_required' => 1],
            ['document_label' => 'Transfer Pricing Documentation', 'is_required' => 1],
            ['document_label' => 'ITR14SD Submission Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 16. SARS Compromise Application
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'SARS Compromise Application (s200)',
        'description' => 'Apply for compromise of tax debt with SARS under s200 of Tax Administration Act',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Assess client financial position', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Determine total outstanding SARS debt', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify client qualifies for compromise (not tax evasion)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare detailed asset and liability statement', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare compromise proposal and motivation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect all supporting financial evidence', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Submit compromise application to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Attend meeting with SARS (if required)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Negotiate terms with SARS', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Receive SARS decision', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Comply with compromise terms', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advise client of outcome and obligations', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company / Taxpayer Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 0],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'SARS Statement of Account', 'is_required' => 1],
            ['document_label' => 'Asset & Liability Statement', 'is_required' => 1],
            ['document_label' => 'Compromise Proposal & Motivation', 'is_required' => 1],
            ['document_label' => 'Supporting Financial Evidence', 'is_required' => 1],
            ['document_label' => 'SARS Decision Letter', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 17. SARS RAV01 - Registered Particulars Update
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'SARS RAV01 - Registered Particulars Update',
        'description' => 'Update taxpayer registered particulars on SARS (address, banking, contact details, representative)',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Identify changes to registered details', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect supporting documents for changes', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Log into eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete RAV01 form with updated details', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Update banking details (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Update physical/postal address (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Update contact person (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Update public officer / representative (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Submit RAV01 to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download confirmation / updated registration', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company / Taxpayer Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_mobile', 'field_label' => 'Mobile', 'is_required' => 0],
            ['field_name' => 'bank_name', 'field_label' => 'Bank Name', 'is_required' => 0],
            ['field_name' => 'bank_account_number', 'field_label' => 'Account Number', 'is_required' => 0],
            ['field_name' => 'bank_branch_code', 'field_label' => 'Branch Code', 'is_required' => 0],
        ],
        'docs' => [
            ['document_label' => 'Supporting Documents (proof of address, bank confirmation, etc.)', 'is_required' => 1],
            ['document_label' => 'RAV01 Submission Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // OUTPUT
    // =====================================================
    echo "<h2 style='color:#28a745;'>SARS Seed Data Complete!</h2>";
    echo "<p><strong>Additional SARS Job Types Created (" . count($seeded) . "):</strong></p><ol>";
    foreach ($seeded as $s) {
        echo "<li>" . htmlspecialchars($s) . "</li>";
    }
    echo "</ol>";

    $totalTypes = DB::table('job_card_types')->count();
    echo "<p><strong>Total job types in system: " . $totalTypes . "</strong></p>";

    echo "<h3>All SARS Job Types (" . DB::table('job_card_types')->where('submission_to', 'LIKE', '%SARS%')->count() . "):</h3><ol>";
    $sarsTypes = DB::table('job_card_types')->where('submission_to', 'LIKE', '%SARS%')->orderBy('display_order')->get();
    foreach ($sarsTypes as $t) {
        $stepCount = DB::table('job_card_type_steps')->where('job_type_id', $t->id)->count();
        echo "<li><strong>" . htmlspecialchars($t->name) . "</strong> — " . $stepCount . " steps</li>";
    }
    echo "</ol>";

    echo "<p>Go to <a href='/job-cards/admin/types'>Job Card Setup</a> to review all types!</p>";

} catch (\Exception $e) {
    echo "<h2 style='color:#dc3545;'>Seed Error</h2>";
    echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
