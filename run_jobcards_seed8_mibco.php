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
    // 1. MIBCO Employer Registration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'MIBCO Employer Registration',
        'description' => 'Register employer with the Motor Industry Bargaining Council (MIBCO)',
        'submission_to' => 'MIBCO',
        'steps' => [
            ['step_name' => 'Verify employer falls within MIBCO scope (motor industry SIC codes)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Determine MIBCO division (Motor Retail, Component Manufacturing, Fuel Retail, etc.)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect COR certificate', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect director/member ID copies', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect employee list with details', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Complete MIBCO registration application form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit registration to MIBCO regional office', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay registration fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive MIBCO employer registration number', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download registration confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Set up monthly contribution schedule', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advise client on MIBCO wage rates and conditions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'trading_name', 'field_label' => 'Trading Name', 'is_required' => 0],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'Director First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Director Surname', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'COR Certificate', 'is_required' => 1],
            ['document_label' => 'Director/Member ID Copies', 'is_required' => 1],
            ['document_label' => 'Employee List', 'is_required' => 1],
            ['document_label' => 'MIBCO Registration Application', 'is_required' => 1],
            ['document_label' => 'MIBCO Registration Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 2. MIBCO Monthly Contributions
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'MIBCO Monthly Contributions',
        'description' => 'Calculate and submit monthly MIBCO contributions (provident fund, sick pay, holiday bonus, admin levy)',
        'submission_to' => 'MIBCO',
        'steps' => [
            ['step_name' => 'Obtain monthly payroll summary', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Calculate MIBCO Provident Fund contributions (employer + employee)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate Sick Pay Fund contributions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate Holiday Bonus Fund contributions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate MIBCO Administration Levy', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate MIBFA (Motor Industry Fund Administrators) contributions', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Complete MIBCO monthly contribution schedule', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit contribution schedule online (MIBCO portal)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Ensure payment by 7th of following month', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission receipt / proof of payment', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Reconcile MIBCO account', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Monthly Payroll Summary', 'is_required' => 1],
            ['document_label' => 'MIBCO Contribution Schedule', 'is_required' => 1],
            ['document_label' => 'Proof of Payment', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 3. MIBCO Annual Reconciliation
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'MIBCO Annual Reconciliation',
        'description' => 'Annual reconciliation of MIBCO contributions — verify all monthly submissions and payments are correct',
        'submission_to' => 'MIBCO',
        'steps' => [
            ['step_name' => 'Obtain annual payroll reports', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Obtain MIBCO statement of account', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Reconcile monthly contributions submitted vs payroll', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Reconcile payments made vs MIBCO records', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Identify and resolve discrepancies', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit correcting schedules (if needed)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Pay any outstanding amounts', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Obtain MIBCO compliance certificate / clearance', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Annual Payroll Reports', 'is_required' => 1],
            ['document_label' => 'MIBCO Statement of Account', 'is_required' => 1],
            ['document_label' => 'Reconciliation Working Paper', 'is_required' => 1],
            ['document_label' => 'MIBCO Compliance Certificate', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 4. MIBCO Provident Fund Withdrawal / Claim
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'MIBCO Provident Fund Withdrawal / Claim',
        'description' => 'Process employee provident fund withdrawal claim on termination through MIBFA',
        'submission_to' => 'MIBCO / MIBFA',
        'steps' => [
            ['step_name' => 'Confirm employee termination and reason', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect employee ID copy', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect employee bank confirmation letter', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Complete MIBFA withdrawal claim form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain employer signature on claim form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain employee signature on claim form', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Apply for tax directive from SARS (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Submit claim to MIBFA', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor claim progress', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm payment to employee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Employer Name', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Employee ID Copy', 'is_required' => 1],
            ['document_label' => 'Bank Confirmation Letter', 'is_required' => 1],
            ['document_label' => 'MIBFA Withdrawal Claim Form (signed)', 'is_required' => 1],
            ['document_label' => 'SARS Tax Directive (if applicable)', 'is_required' => 0],
            ['document_label' => 'Termination Letter', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 5. MIBCO Sick Pay Fund Claim
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'MIBCO Sick Pay Fund Claim',
        'description' => 'Submit employee sick pay claim to MIBCO Sick Pay Fund',
        'submission_to' => 'MIBCO',
        'steps' => [
            ['step_name' => 'Verify employee has exhausted BCEA sick leave entitlement', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect medical certificate from doctor', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect employee ID copy', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Verify employee MIBCO contributions are up to date', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete MIBCO sick pay claim form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain employer signature', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit claim to MIBCO', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor claim progress', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm payment to employee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Employer Name', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Medical Certificate', 'is_required' => 1],
            ['document_label' => 'Employee ID Copy', 'is_required' => 1],
            ['document_label' => 'Sick Pay Claim Form (signed)', 'is_required' => 1],
            ['document_label' => 'Employee Leave Records', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 6. MIBCO Holiday Bonus Fund Claim
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'MIBCO Holiday Bonus Fund Claim',
        'description' => 'Process annual holiday bonus fund claim for eligible employees',
        'submission_to' => 'MIBCO',
        'steps' => [
            ['step_name' => 'Verify employee eligibility for holiday bonus', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify holiday bonus contributions are up to date', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete holiday bonus claim form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'List all eligible employees with details', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain employer signature', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit claim to MIBCO before deadline', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor claim processing', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm payment received', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Distribute holiday bonus to employees', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Holiday Bonus Claim Form', 'is_required' => 1],
            ['document_label' => 'Eligible Employee List', 'is_required' => 1],
            ['document_label' => 'Proof of Payment to Employees', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 7. MIBCO Wage Compliance / Minimum Wage
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'MIBCO Wage Compliance Review',
        'description' => 'Review employer compliance with MIBCO Main Agreement minimum wages, overtime, allowances and conditions',
        'submission_to' => 'MIBCO',
        'steps' => [
            ['step_name' => 'Obtain latest MIBCO Main Agreement wage schedule', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Determine correct job grading for each employee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Compare current wages vs MIBCO prescribed minimums', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Check overtime rates (1.5x normal, 2x Sundays/PH)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Check tool allowance compliance (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Check night shift allowance compliance (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Check standby / call-out allowance (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review working hours compliance (45hrs/week max)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare compliance report with findings', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advise client on adjustments needed', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate back pay (if underpayment found)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Implement wage adjustments in payroll', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'MIBCO Main Agreement (current)', 'is_required' => 1],
            ['document_label' => 'Employee Payroll Details', 'is_required' => 1],
            ['document_label' => 'Wage Compliance Report', 'is_required' => 1],
            ['document_label' => 'Back Pay Calculation (if applicable)', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 8. MIBCO Dispute / Agent Investigation
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'MIBCO Dispute / Agent Investigation',
        'description' => 'Respond to MIBCO agent investigation or dispute referral for non-compliance',
        'submission_to' => 'MIBCO',
        'steps' => [
            ['step_name' => 'Review MIBCO agent notice / complaint', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Identify nature of dispute (wage, contributions, conditions)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect all employment records for affected employees', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect payroll records and MIBCO contribution history', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare response to MIBCO agent', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Attend MIBCO agent meeting / inspection', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Negotiate settlement (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Rectify non-compliance issues', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay any fines or back pay ordered', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Receive MIBCO clearance / resolution', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Advise client of outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'MIBCO Agent Notice / Complaint', 'is_required' => 1],
            ['document_label' => 'Employment Records', 'is_required' => 1],
            ['document_label' => 'Payroll & Contribution Records', 'is_required' => 1],
            ['document_label' => 'Response to MIBCO Agent', 'is_required' => 1],
            ['document_label' => 'Settlement Agreement (if applicable)', 'is_required' => 0],
            ['document_label' => 'MIBCO Clearance / Resolution', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 9. MIBCO Exemption Application
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'MIBCO Exemption Application',
        'description' => 'Apply for exemption from certain provisions of the MIBCO Main Agreement',
        'submission_to' => 'MIBCO',
        'steps' => [
            ['step_name' => 'Identify specific provisions employer seeks exemption from', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare financial justification for exemption', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Consult with employees / shop steward', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain employee consent (if required)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Complete MIBCO exemption application form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect supporting financial documents', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Submit exemption application to MIBCO', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Attend exemption committee hearing (if required)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Receive exemption decision', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Advise client of outcome and conditions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'MIBCO Exemption Application', 'is_required' => 1],
            ['document_label' => 'Financial Statements / Evidence', 'is_required' => 1],
            ['document_label' => 'Employee Consultation Records', 'is_required' => 0],
            ['document_label' => 'Exemption Decision Letter', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 10. MIBCO Compliance Certificate
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'MIBCO Compliance Certificate',
        'description' => 'Obtain MIBCO compliance certificate (required for tenders and some contracts in motor industry)',
        'submission_to' => 'MIBCO',
        'steps' => [
            ['step_name' => 'Verify all monthly contributions are up to date', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify no outstanding disputes or fines', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify wage compliance with Main Agreement', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Request compliance certificate from MIBCO', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay any outstanding amounts (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Monitor certificate processing', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download / receive compliance certificate', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Verify validity dates', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'MIBCO Compliance Certificate', 'is_required' => 1],
            ['document_label' => 'Contribution Payment Receipts', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // OUTPUT
    // =====================================================
    echo "<h2 style='color:#28a745;'>MIBCO Seed Data Complete!</h2>";
    echo "<p><strong>MIBCO Job Types Created (" . count($seeded) . "):</strong></p><ol>";
    foreach ($seeded as $s) {
        echo "<li>" . htmlspecialchars($s) . "</li>";
    }
    echo "</ol>";

    $totalTypes = DB::table('job_card_types')->count();
    echo "<p><strong>Grand Total job types in system: " . $totalTypes . "</strong></p>";

    echo "<h3>Complete Summary by Category:</h3><ul>";
    $categories = DB::table('job_card_types')
        ->selectRaw('submission_to, COUNT(*) as cnt')
        ->groupBy('submission_to')
        ->orderByDesc('cnt')
        ->get();
    foreach ($categories as $c) {
        echo "<li><strong>" . htmlspecialchars($c->submission_to) . "</strong>: " . $c->cnt . " types</li>";
    }
    echo "</ul>";

    echo "<p>Go to <a href='/job-cards/admin/types'>Job Card Setup</a> to review all types!</p>";

} catch (\Exception $e) {
    echo "<h2 style='color:#dc3545;'>Seed Error</h2>";
    echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
