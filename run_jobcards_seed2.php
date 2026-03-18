<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

// Helper function to seed a job type with its steps, fields, and documents
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
    $seeded = [];
    $order = 9; // Start after existing 8

    // =====================================================
    // 9. EMP201 - Monthly PAYE Return
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'EMP201 - Monthly PAYE Return',
        'description' => 'Monthly employer declaration for PAYE, UIF and SDL',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Obtain monthly payroll summary', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Verify PAYE calculated correctly', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify UIF contributions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify SDL levy amount', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete EMP201 on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm payment made by due date', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission receipt', 'step_type' => 'document_required', 'is_required' => 1],
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
            ['document_label' => 'Monthly Payroll Summary', 'is_required' => 1],
            ['document_label' => 'EMP201 Submission Receipt', 'is_required' => 1],
            ['document_label' => 'Proof of Payment', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 10. VAT201 - VAT Return
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'VAT201 - VAT Return',
        'description' => 'Bi-monthly or monthly VAT return submission to SARS',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Obtain VAT period trial balance / reports', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Reconcile output VAT (sales)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Reconcile input VAT (purchases)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Check for disallowed input VAT items', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare VAT reconciliation working paper', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete VAT201 on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm payment / refund status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission receipt', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'vat_number', 'field_label' => 'VAT Number', 'is_required' => 1],
            ['field_name' => 'vat_return_cycle', 'field_label' => 'VAT Return Cycle', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 0],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'VAT Period Trial Balance', 'is_required' => 1],
            ['document_label' => 'VAT Reconciliation Working Paper', 'is_required' => 1],
            ['document_label' => 'VAT201 Submission Receipt', 'is_required' => 1],
            ['document_label' => 'Proof of Payment', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 11. EMP501 - Bi-Annual Employer Reconciliation
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'EMP501 - Employer Reconciliation',
        'description' => 'Bi-annual employer reconciliation (interim Aug and annual Feb)',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Obtain payroll reports for reconciliation period', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Reconcile EMP201s submitted vs payroll totals', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify employee tax certificates (IRP5/IT3a)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Check for variances and correct', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Generate IRP5/IT3(a) certificates', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete EMP501 on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Upload tax certificates', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission receipt', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Distribute IRP5s to employees', 'step_type' => 'checkbox', 'is_required' => 1],
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
            ['document_label' => 'Payroll Reports for Period', 'is_required' => 1],
            ['document_label' => 'EMP201 Submission History', 'is_required' => 1],
            ['document_label' => 'IRP5/IT3(a) Certificates', 'is_required' => 1],
            ['document_label' => 'EMP501 Submission Receipt', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 12. Tax Clearance Certificate (TCC)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Tax Clearance Certificate (TCC)',
        'description' => 'Application for SARS Tax Clearance Certificate (Good Standing)',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify all tax returns are up to date', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify no outstanding SARS debt', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Check all eFiling profiles are linked', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Apply for TCC on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor application status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download Tax Clearance Certificate', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Verify TCC pin on SARS website', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send TCC to client', 'step_type' => 'checkbox', 'is_required' => 1],
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
            ['document_label' => 'Tax Clearance Certificate', 'is_required' => 1],
            ['document_label' => 'SARS Statement of Account', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 13. Objection to SARS Assessment
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Objection to SARS Assessment',
        'description' => 'Filing an objection against a SARS assessment (ADR1)',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Review Notice of Assessment', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Identify grounds for objection', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect supporting documents', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare objection letter / motivation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete ADR1 form on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Upload supporting documents', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit objection to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor objection outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download revised assessment (if allowed)', 'step_type' => 'document_required', 'is_required' => 0],
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
            ['document_label' => 'Notice of Assessment', 'is_required' => 1],
            ['document_label' => 'Objection Letter / Motivation', 'is_required' => 1],
            ['document_label' => 'Supporting Documents', 'is_required' => 1],
            ['document_label' => 'ADR1 Submission Confirmation', 'is_required' => 1],
            ['document_label' => 'Revised Assessment', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 14. Appeal to Tax Board / Tax Court
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Appeal to Tax Board / Tax Court',
        'description' => 'Filing an appeal after disallowed objection (ADR2)',
        'submission_to' => 'SARS / Tax Board',
        'steps' => [
            ['step_name' => 'Review SARS objection outcome', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Assess merits of appeal', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare appeal grounds and motivation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete ADR2 / Notice of Appeal', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit appeal via eFiling or delivery', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare for Tax Board hearing (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Attend hearing / submit written submissions', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Record outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advise client of result', 'step_type' => 'checkbox', 'is_required' => 1],
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
            ['document_label' => 'SARS Objection Outcome Letter', 'is_required' => 1],
            ['document_label' => 'ADR2 / Notice of Appeal', 'is_required' => 1],
            ['document_label' => 'Appeal Motivation / Submissions', 'is_required' => 1],
            ['document_label' => 'Tax Board Ruling', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 15. COIDA / WCA Registration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'COIDA / WCA Registration',
        'description' => 'Registration with Compensation Fund (COIDA) for workplace injuries',
        'submission_to' => 'Dept of Labour',
        'steps' => [
            ['step_name' => 'Verify company and employee details', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Determine industry classification code', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect director/member ID copies', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare W.As.2 registration form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit registration to Compensation Fund', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor registration status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive COIDA registration number', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Update client master with COIDA number', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'wca_coida_number', 'field_label' => 'WCA/COIDA Number', 'is_required' => 0],
            ['field_name' => 'dept_labour_number', 'field_label' => 'Dept of Labour Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'W.As.2 Registration Form', 'is_required' => 1],
            ['document_label' => 'COR Certificate', 'is_required' => 1],
            ['document_label' => 'Director ID Copies', 'is_required' => 1],
            ['document_label' => 'COIDA Registration Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 16. COIDA Return of Earnings (W.As.8)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'COIDA Return of Earnings',
        'description' => 'Annual COIDA Return of Earnings and assessment payment',
        'submission_to' => 'Dept of Labour',
        'steps' => [
            ['step_name' => 'Obtain annual payroll summary', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Calculate total earnings per category', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete W.As.8 Return of Earnings', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit online via CompEasy or manual', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive assessment from Compensation Fund', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Confirm payment of assessment', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain Letter of Good Standing', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'wca_coida_number', 'field_label' => 'WCA/COIDA Number', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Annual Payroll Summary', 'is_required' => 1],
            ['document_label' => 'W.As.8 Return of Earnings', 'is_required' => 1],
            ['document_label' => 'COIDA Assessment Notice', 'is_required' => 1],
            ['document_label' => 'Proof of Payment', 'is_required' => 1],
            ['document_label' => 'Letter of Good Standing', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 17. B-BBEE Affidavit / Certificate
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'B-BBEE Affidavit / Certificate',
        'description' => 'Prepare B-BBEE sworn affidavit or verification for EME/QSE',
        'submission_to' => 'Client / Commissioner of Oaths',
        'steps' => [
            ['step_name' => 'Verify company turnover qualifies as EME/QSE', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Determine B-BBEE level', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify black ownership percentage', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare B-BBEE affidavit', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Client to sign before Commissioner of Oaths', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain commissioned affidavit', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send certified copy to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'Director First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Director Surname', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Latest Annual Financial Statements', 'is_required' => 1],
            ['document_label' => 'Director ID Copies', 'is_required' => 1],
            ['document_label' => 'Signed B-BBEE Affidavit', 'is_required' => 1],
            ['document_label' => 'COR Certificate', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 18. Income Tax Registration (Individual)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Income Tax Registration - Individual',
        'description' => 'Register individual taxpayer with SARS for income tax',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify personal details', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Collect certified ID copy', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect proof of address', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect proof of bank account', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Register on SARS eFiling or at branch', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download IT150 / registration notice', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update client master with tax number', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'director_first_name', 'field_label' => 'First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Surname', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'ID Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_mobile', 'field_label' => 'Mobile', 'is_required' => 1],
            ['field_name' => 'bank_name', 'field_label' => 'Bank Name', 'is_required' => 1],
            ['field_name' => 'bank_account_number', 'field_label' => 'Account Number', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Certified ID Copy', 'is_required' => 1],
            ['document_label' => 'Proof of Address', 'is_required' => 1],
            ['document_label' => 'Proof of Bank Account', 'is_required' => 1],
            ['document_label' => 'SARS Registration Notice', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 19. Income Tax Registration (Company)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Income Tax Registration - Company',
        'description' => 'Register company/CC for income tax with SARS',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify CIPC registration is complete', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect COR certificate', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect director ID copies', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect proof of business address', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect proof of bank account', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Register on SARS eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download registration notice', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update client master with tax number', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 0],
            ['field_name' => 'director_first_name', 'field_label' => 'Director First Name', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'Director ID Number', 'is_required' => 1],
            ['field_name' => 'bank_name', 'field_label' => 'Bank Name', 'is_required' => 1],
            ['field_name' => 'bank_account_number', 'field_label' => 'Account Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'COR Certificate', 'is_required' => 1],
            ['document_label' => 'Director ID Copies', 'is_required' => 1],
            ['document_label' => 'Proof of Business Address', 'is_required' => 1],
            ['document_label' => 'Proof of Bank Account', 'is_required' => 1],
            ['document_label' => 'SARS Registration Notice', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 20. CC to Pty Ltd Conversion
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CC to Pty Ltd Conversion',
        'description' => 'Convert Close Corporation to Private Company (Pty) Ltd',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Review CC details on CIPC', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Verify all annual returns are up to date', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect member ID copies', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare MOI for new Pty Ltd', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete CoR conversion forms', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit conversion to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay conversion fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor conversion status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download new COR certificate', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update SARS records', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send all documents to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'CC Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'CK Number', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'Member First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Member Surname', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Existing CK Certificate', 'is_required' => 1],
            ['document_label' => 'Member ID Copies', 'is_required' => 1],
            ['document_label' => 'New MOI', 'is_required' => 1],
            ['document_label' => 'New COR Certificate', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 21. CIPC Amendment (Director/Address Changes)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CIPC Amendment - Director/Address',
        'description' => 'Change of directors, registered address or other company details at CIPC',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Verify current company details on CIPC', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Determine changes required', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect new director ID copy (if applicable)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Collect proof of new address (if applicable)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Prepare CoR amendment forms', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit amendment to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay amendment fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor amendment status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download updated company documents', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'number_of_directors', 'field_label' => 'Number of Directors', 'is_required' => 0],
            ['field_name' => 'bizportal_number', 'field_label' => 'BizPortal Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Current COR Certificate', 'is_required' => 1],
            ['document_label' => 'New Director ID Copy', 'is_required' => 0],
            ['document_label' => 'Proof of New Address', 'is_required' => 0],
            ['document_label' => 'Updated CIPC Documents', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 22. Company Deregistration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Company Deregistration',
        'description' => 'Voluntary deregistration of company or CC with CIPC',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Verify company has no outstanding returns', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify no outstanding SARS debt', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm company has ceased trading', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare CoR40.1 deregistration application', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor deregistration status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download deregistration confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Deregister with SARS (income tax, VAT, PAYE)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'vat_number', 'field_label' => 'VAT Number', 'is_required' => 0],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'CoR40.1 Deregistration Form', 'is_required' => 1],
            ['document_label' => 'CIPC Deregistration Confirmation', 'is_required' => 1],
            ['document_label' => 'SARS Deregistration Letters', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 23. VAT Deregistration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'VAT Deregistration',
        'description' => 'Voluntary deregistration from VAT with SARS',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify turnover below threshold or ceased trading', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit final VAT201 return', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete VAT123 deregistration form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit deregistration on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor deregistration status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download deregistration confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'vat_number', 'field_label' => 'VAT Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Final VAT201 Return', 'is_required' => 1],
            ['document_label' => 'VAT Deregistration Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 24. Trust Registration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Trust Registration',
        'description' => 'Registration of a new trust with the Master of the High Court',
        'submission_to' => 'Master of High Court',
        'steps' => [
            ['step_name' => 'Confirm trust type (inter vivos / testamentary)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Draft or review trust deed', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect trustee ID copies', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect trustee acceptance forms', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare J401 application', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to Master of High Court', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay registration fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor registration status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive Letters of Authority', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Register trust for income tax with SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Open bank account for trust', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Send all documents to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'company_name', 'field_label' => 'Trust Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Trust Number (IT)', 'is_required' => 0],
            ['field_name' => 'director_first_name', 'field_label' => 'Founder First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Founder Surname', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'Founder ID Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Trust Tax Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_mobile', 'field_label' => 'Mobile', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Trust Deed (signed)', 'is_required' => 1],
            ['document_label' => 'Trustee ID Copies', 'is_required' => 1],
            ['document_label' => 'Trustee Acceptance Forms', 'is_required' => 1],
            ['document_label' => 'J401 Application', 'is_required' => 1],
            ['document_label' => 'Letters of Authority', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 25. ITR12T - Trust Tax Return
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'ITR12T - Trust Tax Return',
        'description' => 'Annual income tax return for trusts',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify trust details and registration', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Obtain trust financial records', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare trust financial statements', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Determine distributions to beneficiaries', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate trust taxable income', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete ITR12T on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download assessment', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send to trustees', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Trust Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Trust Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Trust Tax Number', 'is_required' => 1],
            ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Trust Financial Statements', 'is_required' => 1],
            ['document_label' => 'Bank Statements (12 months)', 'is_required' => 1],
            ['document_label' => 'Beneficiary Distribution Schedule', 'is_required' => 1],
            ['document_label' => 'SARS Notice of Assessment', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 26. Deceased Estate Administration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Deceased Estate Administration',
        'description' => 'Administration and winding up of a deceased estate',
        'submission_to' => 'Master of High Court / SARS',
        'steps' => [
            ['step_name' => 'Obtain death certificate', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Obtain will (if any)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Report estate to Master of High Court', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive Letters of Executorship', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Open estate bank account', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advertise for creditors (Govt Gazette)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Compile asset and liability inventory', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File final ITR12 for deceased', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare Liquidation & Distribution account', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Lodge L&D account with Master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advertise L&D account for inspection', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Distribute estate to heirs', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File final accounts with Master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'director_first_name', 'field_label' => 'Deceased First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Deceased Surname', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'Deceased ID Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Deceased Tax Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Executor Email', 'is_required' => 1],
            ['field_name' => 'phone_mobile', 'field_label' => 'Executor Mobile', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Death Certificate', 'is_required' => 1],
            ['document_label' => 'Will', 'is_required' => 0],
            ['document_label' => 'Letters of Executorship', 'is_required' => 1],
            ['document_label' => 'Deceased ID Copy', 'is_required' => 1],
            ['document_label' => 'Heir ID Copies', 'is_required' => 1],
            ['document_label' => 'Asset Inventory', 'is_required' => 1],
            ['document_label' => 'L&D Account', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 27. Transfer Duty
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Transfer Duty',
        'description' => 'Transfer duty declaration for property acquisitions',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Obtain sale agreement / property details', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Determine transfer duty applicable', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate transfer duty amount', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete transfer duty declaration on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm payment', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download transfer duty receipt', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send receipt to conveyancer/client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Buyer Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'Buyer ID Number', 'is_required' => 0],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Sale Agreement / Deed of Sale', 'is_required' => 1],
            ['document_label' => 'Buyer ID Copy', 'is_required' => 1],
            ['document_label' => 'Transfer Duty Receipt', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 28. Donations Tax (IT144)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Donations Tax (IT144)',
        'description' => 'Donations tax declaration and payment',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Verify donation details and amount', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Check annual donations tax exemption', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate donations tax at 20%', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete IT144 form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm payment by donor', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission receipt', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Donor Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'Donor ID Number', 'is_required' => 0],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Donation Agreement / Letter', 'is_required' => 1],
            ['document_label' => 'IT144 Submission Receipt', 'is_required' => 1],
            ['document_label' => 'Proof of Payment', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 29. Tax Directive Application
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Tax Directive Application',
        'description' => 'Apply for SARS tax directive (lump sums, gratuity, pension)',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Determine type of directive needed', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect relevant supporting documents', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Verify taxpayer details on SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete tax directive application on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor directive status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download tax directive', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send directive to employer/fund', 'step_type' => 'checkbox', 'is_required' => 1],
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
            ['document_label' => 'Supporting Documents (pension/fund letter)', 'is_required' => 1],
            ['document_label' => 'Tax Directive', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 30. SARS Audit / Verification
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'SARS Audit / Verification',
        'description' => 'Respond to SARS audit or verification request',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Review SARS audit letter / notification', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Identify documents requested by SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect all requested documents from client', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare supporting schedules / working papers', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Upload documents to eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit response to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor audit progress', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Respond to additional queries (if any)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review audit outcome / revised assessment', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advise client of outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company / Taxpayer Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'vat_number', 'field_label' => 'VAT Number', 'is_required' => 0],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'SARS Audit Letter / Notification', 'is_required' => 1],
            ['document_label' => 'Requested Supporting Documents', 'is_required' => 1],
            ['document_label' => 'Working Papers / Schedules', 'is_required' => 1],
            ['document_label' => 'SARS Audit Outcome / Assessment', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 31. Monthly Payroll Processing
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Monthly Payroll Processing',
        'description' => 'Monthly payroll calculation, payslips and submissions',
        'submission_to' => 'Internal / Client',
        'steps' => [
            ['step_name' => 'Collect monthly payroll input from client', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Capture overtime, leave, commissions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Process payroll calculations', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Generate payslips', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Generate payroll summary report', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send payroll pack to client for approval', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Client approval received', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Generate payment file / EFT batch', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Distribute payslips to employees', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 1],
            ['field_name' => 'uif_number', 'field_label' => 'UIF Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 0],
        ],
        'docs' => [
            ['document_label' => 'Monthly Payroll Input Sheet', 'is_required' => 1],
            ['document_label' => 'Payroll Summary Report', 'is_required' => 1],
            ['document_label' => 'Payslips', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 32. Monthly Bookkeeping
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Monthly Bookkeeping',
        'description' => 'Monthly bookkeeping and reconciliation for client',
        'submission_to' => 'Internal / Client',
        'steps' => [
            ['step_name' => 'Collect bank statements for the month', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect invoices and receipts', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Capture transactions in accounting system', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Process petty cash entries', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Complete bank reconciliation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Reconcile debtors and creditors', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Generate management reports', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review for anomalies or queries', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send reports to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'bank_name', 'field_label' => 'Bank Name', 'is_required' => 1],
            ['field_name' => 'bank_account_number', 'field_label' => 'Account Number', 'is_required' => 1],
            ['field_name' => 'vat_number', 'field_label' => 'VAT Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Bank Statements', 'is_required' => 1],
            ['document_label' => 'Invoices & Receipts', 'is_required' => 1],
            ['document_label' => 'Bank Reconciliation', 'is_required' => 1],
            ['document_label' => 'Management Reports', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 33. UIF Declaration (UI-19)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'UIF Declaration (UI-19)',
        'description' => 'Monthly UIF declaration on uFiling',
        'submission_to' => 'Dept of Labour (uFiling)',
        'steps' => [
            ['step_name' => 'Obtain monthly payroll with UIF details', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Log into uFiling portal', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete UI-19 declaration', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify employee details match', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit declaration', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission receipt', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'uif_number', 'field_label' => 'UIF Number', 'is_required' => 1],
            ['field_name' => 'dept_labour_number', 'field_label' => 'Dept of Labour Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Monthly Payroll UIF Summary', 'is_required' => 1],
            ['document_label' => 'UI-19 Submission Receipt', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 34. Customs & Excise Registration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Customs & Excise Registration',
        'description' => 'Register for customs and/or excise duties with SARS',
        'submission_to' => 'SARS Customs',
        'steps' => [
            ['step_name' => 'Determine type of customs registration needed', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify company details', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Collect supporting documents', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare customs registration application', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS Customs', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor registration status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download registration confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'COR Certificate', 'is_required' => 1],
            ['document_label' => 'Director ID Copies', 'is_required' => 1],
            ['document_label' => 'Customs Registration Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 35. Independent Review / Audit
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Independent Review / Audit',
        'description' => 'Independent review or audit of company financial statements',
        'submission_to' => 'Client / CIPC',
        'steps' => [
            ['step_name' => 'Send engagement letter to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive signed engagement letter', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Obtain financial statements and trial balance', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Plan review / audit procedures', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Perform analytical review procedures', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Make enquiries of management', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify bank confirmations', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Document findings and conclusions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Draft review / audit report', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Issue signed report', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send report to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all working papers', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Signed Engagement Letter', 'is_required' => 1],
            ['document_label' => 'Trial Balance', 'is_required' => 1],
            ['document_label' => 'Financial Statements', 'is_required' => 1],
            ['document_label' => 'Bank Confirmations', 'is_required' => 0],
            ['document_label' => 'Review / Audit Report', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 36. SARS Power of Attorney / eFiling Linking
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'SARS Power of Attorney / eFiling Link',
        'description' => 'Register as tax practitioner and link client on eFiling',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Obtain signed Power of Attorney from client', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect client ID copy', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Log into SARS eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Request client portfolio linking', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Upload Power of Attorney on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor linking status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify all tax types are linked', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Update client master with eFiling status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company / Taxpayer Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'ID Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Signed Power of Attorney', 'is_required' => 1],
            ['document_label' => 'Client ID Copy', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 37. SARS Voluntary Disclosure (VDP)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'SARS Voluntary Disclosure (VDP)',
        'description' => 'Voluntary disclosure programme application to SARS',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Assess qualification for VDP', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Identify tax defaults to disclose', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect all relevant records', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Calculate outstanding tax liability', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare VDP application', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit VDP application to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor application status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive VDP agreement', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'File outstanding returns', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm payment of assessed liability', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advise client of outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company / Taxpayer Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'ID Number', 'is_required' => 0],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'VDP Application', 'is_required' => 1],
            ['document_label' => 'Supporting Financial Records', 'is_required' => 1],
            ['document_label' => 'VDP Agreement from SARS', 'is_required' => 1],
            ['document_label' => 'Proof of Payment', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 38. SARS Debt Management / Payment Arrangement
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'SARS Payment Arrangement',
        'description' => 'Request instalment payment arrangement for outstanding SARS debt',
        'submission_to' => 'SARS',
        'steps' => [
            ['step_name' => 'Obtain SARS Statement of Account', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Verify total outstanding amount', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare client financial position summary', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Propose instalment plan', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete deferment / instalment request on eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor approval status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download approval / arrangement letter', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Advise client of payment schedule', 'step_type' => 'checkbox', 'is_required' => 1],
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
            ['document_label' => 'SARS Statement of Account', 'is_required' => 1],
            ['document_label' => 'Client Financial Position Summary', 'is_required' => 1],
            ['document_label' => 'SARS Approval / Arrangement Letter', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // OUTPUT
    // =====================================================
    echo "<h2 style='color:#28a745;'>Seed Data Batch 2 Complete!</h2>";
    echo "<p><strong>Additional Job Types Created (" . count($seeded) . "):</strong></p><ol start='9'>";
    foreach ($seeded as $s) {
        echo "<li>" . htmlspecialchars($s) . "</li>";
    }
    echo "</ol>";
    echo "<p>Each type has been pre-configured with <strong>Steps</strong>, <strong>Client Fields</strong>, and <strong>Document Requirements</strong>.</p>";
    echo "<p>Total job types now: 8 (original) + " . count($seeded) . " (new) = <strong>" . (8 + count($seeded)) . "</strong></p>";
    echo "<p>Go to <a href='/job-cards/admin/types'>Job Card Setup</a> to review all types!</p>";

} catch (\Exception $e) {
    echo "<h2 style='color:#dc3545;'>Seed Error</h2>";
    echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
