<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

try {
    $seeded = [];

    // =====================================================
    // 1. ITR12 — Individual Tax Return
    // =====================================================
    $typeId = DB::table('job_card_types')->insertGetId([
        'name' => 'ITR12 - Individual Tax Return',
        'description' => 'Individual income tax return submission to SARS',
        'submission_to' => 'SARS',
        'display_order' => 1,
        'is_active' => 1,
        'created_by' => 1,
        'updated_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $seeded[] = 'ITR12 - Individual Tax Return';

    // Steps
    $steps = [
        ['step_name' => 'Verify client personal details', 'step_type' => 'info_review', 'is_required' => 1],
        ['step_name' => 'Collect IRP5/IT3(a) certificates', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Collect medical aid certificate', 'step_type' => 'document_required', 'is_required' => 0],
        ['step_name' => 'Collect retirement annuity certificate', 'step_type' => 'document_required', 'is_required' => 0],
        ['step_name' => 'Collect travel logbook (if applicable)', 'step_type' => 'document_required', 'is_required' => 0],
        ['step_name' => 'Verify bank details for refund', 'step_type' => 'info_review', 'is_required' => 1],
        ['step_name' => 'Calculate taxable income and liability', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Complete ITR12 on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Download Notice of Assessment', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Review assessment for errors', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Send assessment to client', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
    ];
    foreach ($steps as $i => $s) {
        DB::table('job_card_type_steps')->insert(array_merge($s, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    // Fields
    $fields = [
        ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
        ['field_name' => 'director_first_name', 'field_label' => 'First Name', 'is_required' => 1],
        ['field_name' => 'director_surname', 'field_label' => 'Surname', 'is_required' => 1],
        ['field_name' => 'director_id_number', 'field_label' => 'ID Number', 'is_required' => 1],
        ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
        ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ['field_name' => 'phone_mobile', 'field_label' => 'Mobile', 'is_required' => 0],
        ['field_name' => 'bank_name', 'field_label' => 'Bank Name', 'is_required' => 1],
        ['field_name' => 'bank_account_number', 'field_label' => 'Bank Account Number', 'is_required' => 1],
        ['field_name' => 'bank_branch_code', 'field_label' => 'Branch Code', 'is_required' => 1],
        ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
    ];
    foreach ($fields as $i => $f) {
        DB::table('job_card_type_fields')->insert(array_merge($f, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    // Documents
    $docs = [
        ['document_label' => 'ID Copy', 'is_required' => 1],
        ['document_label' => 'IRP5 / IT3(a) Certificate', 'is_required' => 1],
        ['document_label' => 'Medical Aid Tax Certificate', 'is_required' => 0],
        ['document_label' => 'Retirement Annuity Certificate', 'is_required' => 0],
        ['document_label' => 'Travel Logbook', 'is_required' => 0],
        ['document_label' => 'Proof of Bank Details', 'is_required' => 1],
        ['document_label' => 'SARS Notice of Assessment', 'is_required' => 1],
    ];
    foreach ($docs as $i => $d) {
        DB::table('job_card_type_documents')->insert(array_merge($d, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    // =====================================================
    // 2. ITR14 — Company Tax Return
    // =====================================================
    $typeId = DB::table('job_card_types')->insertGetId([
        'name' => 'ITR14 - Company Tax Return',
        'description' => 'Company income tax return submission to SARS',
        'submission_to' => 'SARS',
        'display_order' => 2,
        'is_active' => 1,
        'created_by' => 1, 'updated_by' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $seeded[] = 'ITR14 - Company Tax Return';

    $steps = [
        ['step_name' => 'Verify company details and registration', 'step_type' => 'info_review', 'is_required' => 1],
        ['step_name' => 'Obtain signed Annual Financial Statements', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Verify tax computation and adjustments', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Check assessed losses brought forward', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Prepare ITR14 working papers', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Complete ITR14 on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Download Notice of Assessment', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Review assessment', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Send assessment to client', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
    ];
    foreach ($steps as $i => $s) {
        DB::table('job_card_type_steps')->insert(array_merge($s, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $fields = [
        ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
        ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
        ['field_name' => 'company_reg_number', 'field_label' => 'Company Registration', 'is_required' => 1],
        ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
        ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
        ['field_name' => 'vat_number', 'field_label' => 'VAT Number', 'is_required' => 0],
        ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
        ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 0],
    ];
    foreach ($fields as $i => $f) {
        DB::table('job_card_type_fields')->insert(array_merge($f, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $docs = [
        ['document_label' => 'Signed Annual Financial Statements', 'is_required' => 1],
        ['document_label' => 'Tax Computation Working Papers', 'is_required' => 1],
        ['document_label' => 'SARS Notice of Assessment', 'is_required' => 1],
        ['document_label' => 'Company COR Certificate', 'is_required' => 0],
    ];
    foreach ($docs as $i => $d) {
        DB::table('job_card_type_documents')->insert(array_merge($d, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    // =====================================================
    // 3. CIPC Annual Return
    // =====================================================
    $typeId = DB::table('job_card_types')->insertGetId([
        'name' => 'CIPC Annual Return',
        'description' => 'Annual return filing with CIPC for companies and close corporations',
        'submission_to' => 'CIPC',
        'display_order' => 3,
        'is_active' => 1,
        'created_by' => 1, 'updated_by' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $seeded[] = 'CIPC Annual Return';

    $steps = [
        ['step_name' => 'Verify company registration details on CIPC', 'step_type' => 'info_review', 'is_required' => 1],
        ['step_name' => 'Verify director details are current', 'step_type' => 'info_review', 'is_required' => 1],
        ['step_name' => 'Verify registered address is current', 'step_type' => 'info_review', 'is_required' => 1],
        ['step_name' => 'Check outstanding annual returns', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Log into CIPC portal', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Complete annual return form', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Make payment for filing fee', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Submit annual return', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Download confirmation / receipt', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
    ];
    foreach ($steps as $i => $s) {
        DB::table('job_card_type_steps')->insert(array_merge($s, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $fields = [
        ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
        ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
        ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
        ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
        ['field_name' => 'company_reg_date', 'field_label' => 'Registration Date', 'is_required' => 0],
        ['field_name' => 'number_of_directors', 'field_label' => 'Number of Directors', 'is_required' => 0],
        ['field_name' => 'cipc_annual_returns', 'field_label' => 'CIPC Annual Returns Status', 'is_required' => 0],
        ['field_name' => 'bizportal_number', 'field_label' => 'BizPortal Number', 'is_required' => 0],
        ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
    ];
    foreach ($fields as $i => $f) {
        DB::table('job_card_type_fields')->insert(array_merge($f, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $docs = [
        ['document_label' => 'COR Certificate', 'is_required' => 1],
        ['document_label' => 'CIPC Filing Receipt', 'is_required' => 1],
        ['document_label' => 'Director ID Copies', 'is_required' => 0],
    ];
    foreach ($docs as $i => $d) {
        DB::table('job_card_type_documents')->insert(array_merge($d, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    // =====================================================
    // 4. VAT Registration
    // =====================================================
    $typeId = DB::table('job_card_types')->insertGetId([
        'name' => 'VAT Registration',
        'description' => 'New VAT vendor registration with SARS',
        'submission_to' => 'SARS',
        'display_order' => 4,
        'is_active' => 1,
        'created_by' => 1, 'updated_by' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $seeded[] = 'VAT Registration';

    $steps = [
        ['step_name' => 'Verify client meets VAT threshold (R1m)', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Collect proof of trading activity', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Verify bank account details', 'step_type' => 'info_review', 'is_required' => 1],
        ['step_name' => 'Prepare VAT registration application', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Submit VAT101 on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Monitor registration status', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Download VAT registration certificate', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Update client master with VAT number', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
    ];
    foreach ($steps as $i => $s) {
        DB::table('job_card_type_steps')->insert(array_merge($s, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $fields = [
        ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
        ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
        ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
        ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
        ['field_name' => 'vat_number', 'field_label' => 'VAT Number', 'is_required' => 0],
        ['field_name' => 'bank_name', 'field_label' => 'Bank Name', 'is_required' => 1],
        ['field_name' => 'bank_account_number', 'field_label' => 'Bank Account Number', 'is_required' => 1],
        ['field_name' => 'bank_branch_code', 'field_label' => 'Branch Code', 'is_required' => 1],
        ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
        ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
    ];
    foreach ($fields as $i => $f) {
        DB::table('job_card_type_fields')->insert(array_merge($f, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $docs = [
        ['document_label' => 'COR Certificate', 'is_required' => 1],
        ['document_label' => 'Proof of Bank Details', 'is_required' => 1],
        ['document_label' => 'Proof of Trading Activity (invoices/contracts)', 'is_required' => 1],
        ['document_label' => 'Director/Member ID Copy', 'is_required' => 1],
        ['document_label' => 'VAT Registration Certificate', 'is_required' => 1],
    ];
    foreach ($docs as $i => $d) {
        DB::table('job_card_type_documents')->insert(array_merge($d, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    // =====================================================
    // 5. PAYE / UIF / SDL Registration
    // =====================================================
    $typeId = DB::table('job_card_types')->insertGetId([
        'name' => 'PAYE / UIF / SDL Registration',
        'description' => 'Employer payroll registration with SARS (PAYE, UIF, SDL)',
        'submission_to' => 'SARS',
        'display_order' => 5,
        'is_active' => 1,
        'created_by' => 1, 'updated_by' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $seeded[] = 'PAYE / UIF / SDL Registration';

    $steps = [
        ['step_name' => 'Verify company details', 'step_type' => 'info_review', 'is_required' => 1],
        ['step_name' => 'Confirm number of employees and salaries', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Collect director/member ID copies', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Prepare EMP101 application', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Submit EMP101 on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Monitor registration status', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Download registration notice', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Register for UIF on uFiling (if required)', 'step_type' => 'checkbox', 'is_required' => 0],
        ['step_name' => 'Update client master with PAYE/UIF/SDL numbers', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
    ];
    foreach ($steps as $i => $s) {
        DB::table('job_card_type_steps')->insert(array_merge($s, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $fields = [
        ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
        ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
        ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
        ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
        ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
        ['field_name' => 'uif_number', 'field_label' => 'UIF Number', 'is_required' => 0],
        ['field_name' => 'sdl_number', 'field_label' => 'SDL Number', 'is_required' => 0],
        ['field_name' => 'dept_labour_number', 'field_label' => 'Dept of Labour Number', 'is_required' => 0],
        ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 0],
    ];
    foreach ($fields as $i => $f) {
        DB::table('job_card_type_fields')->insert(array_merge($f, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $docs = [
        ['document_label' => 'COR Certificate', 'is_required' => 1],
        ['document_label' => 'Director/Member ID Copy', 'is_required' => 1],
        ['document_label' => 'SARS Registration Notice', 'is_required' => 1],
        ['document_label' => 'UIF Registration Confirmation', 'is_required' => 0],
    ];
    foreach ($docs as $i => $d) {
        DB::table('job_card_type_documents')->insert(array_merge($d, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    // =====================================================
    // 6. Annual Financial Statements
    // =====================================================
    $typeId = DB::table('job_card_types')->insertGetId([
        'name' => 'Annual Financial Statements',
        'description' => 'Preparation and signing of annual financial statements',
        'submission_to' => 'Internal / Client',
        'display_order' => 6,
        'is_active' => 1,
        'created_by' => 1, 'updated_by' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $seeded[] = 'Annual Financial Statements';

    $steps = [
        ['step_name' => 'Obtain trial balance from accounting system', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Verify bank reconciliation is up to date', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Verify debtors and creditors reconciliation', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Process year-end adjustments', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Calculate depreciation', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Prepare draft financial statements', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Internal review of draft', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Present to client for review', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Address client queries', 'step_type' => 'checkbox', 'is_required' => 0],
        ['step_name' => 'Obtain client signature on AFS', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Finalize and issue financial statements', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'File all working papers and documents', 'step_type' => 'checkbox', 'is_required' => 1],
    ];
    foreach ($steps as $i => $s) {
        DB::table('job_card_type_steps')->insert(array_merge($s, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $fields = [
        ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
        ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
        ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
        ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
        ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
        ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 0],
        ['field_name' => 'vat_number', 'field_label' => 'VAT Number', 'is_required' => 0],
        ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
    ];
    foreach ($fields as $i => $f) {
        DB::table('job_card_type_fields')->insert(array_merge($f, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $docs = [
        ['document_label' => 'Trial Balance', 'is_required' => 1],
        ['document_label' => 'Bank Statements (12 months)', 'is_required' => 1],
        ['document_label' => 'Debtors Age Analysis', 'is_required' => 0],
        ['document_label' => 'Creditors Age Analysis', 'is_required' => 0],
        ['document_label' => 'Fixed Asset Register', 'is_required' => 0],
        ['document_label' => 'Signed Annual Financial Statements', 'is_required' => 1],
    ];
    foreach ($docs as $i => $d) {
        DB::table('job_card_type_documents')->insert(array_merge($d, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    // =====================================================
    // 7. Provisional Tax (IRP6)
    // =====================================================
    $typeId = DB::table('job_card_types')->insertGetId([
        'name' => 'IRP6 - Provisional Tax',
        'description' => 'First and second provisional tax estimates and payments',
        'submission_to' => 'SARS',
        'display_order' => 7,
        'is_active' => 1,
        'created_by' => 1, 'updated_by' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $seeded[] = 'IRP6 - Provisional Tax';

    $steps = [
        ['step_name' => 'Determine provisional tax period (1st or 2nd)', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Obtain latest management accounts', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Estimate taxable income for the year', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Calculate provisional tax amount', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Complete IRP6 on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Confirm payment made by client', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Download payment receipt', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
    ];
    foreach ($steps as $i => $s) {
        DB::table('job_card_type_steps')->insert(array_merge($s, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $fields = [
        ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
        ['field_name' => 'company_name', 'field_label' => 'Company / Taxpayer Name', 'is_required' => 1],
        ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
        ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
        ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
        ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
    ];
    foreach ($fields as $i => $f) {
        DB::table('job_card_type_fields')->insert(array_merge($f, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $docs = [
        ['document_label' => 'Management Accounts / Trial Balance', 'is_required' => 1],
        ['document_label' => 'IRP6 Submission Confirmation', 'is_required' => 1],
        ['document_label' => 'Proof of Payment', 'is_required' => 1],
    ];
    foreach ($docs as $i => $d) {
        DB::table('job_card_type_documents')->insert(array_merge($d, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    // =====================================================
    // 8. New Company Registration
    // =====================================================
    $typeId = DB::table('job_card_types')->insertGetId([
        'name' => 'New Company Registration',
        'description' => 'Register a new company (Pty Ltd) with CIPC',
        'submission_to' => 'CIPC',
        'display_order' => 8,
        'is_active' => 1,
        'created_by' => 1, 'updated_by' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $seeded[] = 'New Company Registration';

    $steps = [
        ['step_name' => 'Perform name reservation on CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Collect director ID copies', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Collect proof of address for directors', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Prepare MOI (Memorandum of Incorporation)', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Complete CoR registration forms', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Submit to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Pay registration fee', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Monitor registration status', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Download COR certificate', 'step_type' => 'document_required', 'is_required' => 1],
        ['step_name' => 'Create client record in CIMS', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Register for Income Tax', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'Send all documents to client', 'step_type' => 'checkbox', 'is_required' => 1],
        ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
    ];
    foreach ($steps as $i => $s) {
        DB::table('job_card_type_steps')->insert(array_merge($s, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $fields = [
        ['field_name' => 'company_name', 'field_label' => 'Proposed Company Name', 'is_required' => 1],
        ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
        ['field_name' => 'director_first_name', 'field_label' => 'Director First Name', 'is_required' => 1],
        ['field_name' => 'director_surname', 'field_label' => 'Director Surname', 'is_required' => 1],
        ['field_name' => 'director_id_number', 'field_label' => 'Director ID Number', 'is_required' => 1],
        ['field_name' => 'number_of_directors', 'field_label' => 'Number of Directors', 'is_required' => 1],
        ['field_name' => 'number_of_shares', 'field_label' => 'Number of Shares', 'is_required' => 0],
        ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ['field_name' => 'phone_mobile', 'field_label' => 'Mobile', 'is_required' => 1],
    ];
    foreach ($fields as $i => $f) {
        DB::table('job_card_type_fields')->insert(array_merge($f, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    $docs = [
        ['document_label' => 'Director ID Copies', 'is_required' => 1],
        ['document_label' => 'Director Proof of Address', 'is_required' => 1],
        ['document_label' => 'Name Reservation Confirmation', 'is_required' => 1],
        ['document_label' => 'Memorandum of Incorporation (MOI)', 'is_required' => 1],
        ['document_label' => 'COR Certificate', 'is_required' => 1],
    ];
    foreach ($docs as $i => $d) {
        DB::table('job_card_type_documents')->insert(array_merge($d, [
            'job_type_id' => $typeId, 'display_order' => $i + 1, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]));
    }

    echo "<h2 style='color:#28a745;'>Seed Data Complete!</h2>";
    echo "<p><strong>Job Types Created (" . count($seeded) . "):</strong></p><ol>";
    foreach ($seeded as $s) {
        echo "<li>" . htmlspecialchars($s) . "</li>";
    }
    echo "</ol>";
    echo "<p>Each type has been pre-configured with <strong>Steps</strong>, <strong>Client Fields</strong>, and <strong>Document Requirements</strong>.</p>";
    echo "<p>Go to <a href='/job-cards/dashboard'>Job Cards Dashboard</a> to get started!</p>";

} catch (\Exception $e) {
    echo "<h2 style='color:#dc3545;'>Seed Error</h2>";
    echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
