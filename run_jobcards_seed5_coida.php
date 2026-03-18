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
    // 1. COIDA Workplace Injury Claim (W.Cl.1 / W.Cl.2)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'COIDA Workplace Injury Claim',
        'description' => 'Report and claim for workplace injury or occupational disease with the Compensation Fund',
        'submission_to' => 'Dept of Labour / Compensation Fund',
        'steps' => [
            ['step_name' => 'Obtain incident details from employer', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Verify employer is registered with COIDA', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect first medical report from doctor (W.Cl.4)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Complete Employer Report of Accident (W.Cl.2)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete Notice of Accident (W.Cl.1) if applicable', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Collect injured employee ID copy', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect payslip / salary details of injured employee', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Submit claim to Compensation Fund within 7 days', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain claim reference number', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor claim progress', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect progress medical reports (W.Cl.5)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Collect final medical report (W.Cl.5)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Receive adjudication outcome from CF', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Advise employer and employee of outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Employer Name', 'is_required' => 1],
            ['field_name' => 'wca_coida_number', 'field_label' => 'COIDA/WCA Number', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'W.Cl.1 Notice of Accident', 'is_required' => 0],
            ['document_label' => 'W.Cl.2 Employer Report of Accident', 'is_required' => 1],
            ['document_label' => 'W.Cl.4 First Medical Report', 'is_required' => 1],
            ['document_label' => 'Injured Employee ID Copy', 'is_required' => 1],
            ['document_label' => 'Injured Employee Payslip', 'is_required' => 1],
            ['document_label' => 'W.Cl.5 Progress/Final Medical Report', 'is_required' => 1],
            ['document_label' => 'CF Adjudication Outcome', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 2. COIDA Letter of Good Standing
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'COIDA Letter of Good Standing',
        'description' => 'Apply for Letter of Good Standing from the Compensation Fund (required for tenders and contracts)',
        'submission_to' => 'Dept of Labour / Compensation Fund',
        'steps' => [
            ['step_name' => 'Verify employer COIDA registration is active', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify all Return of Earnings (W.As.8) are submitted', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify all assessments are paid', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Apply for Letter of Good Standing on CompEasy portal', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay any outstanding amounts (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Monitor application status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download Letter of Good Standing', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Verify validity dates on certificate', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'wca_coida_number', 'field_label' => 'COIDA/WCA Number', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Return of Earnings Receipts', 'is_required' => 1],
            ['document_label' => 'Assessment Payment Receipts', 'is_required' => 1],
            ['document_label' => 'Letter of Good Standing', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 3. COIDA Assessment Review / Objection
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'COIDA Assessment Review / Objection',
        'description' => 'Object to COIDA assessment amount or tariff classification',
        'submission_to' => 'Dept of Labour / Compensation Fund',
        'steps' => [
            ['step_name' => 'Review COIDA assessment notice', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Identify grounds for objection (tariff rate, earnings amount, classification)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect supporting payroll records', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare objection letter with motivation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit objection to Compensation Fund', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor objection status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive revised assessment (if allowed)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Advise client of outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'wca_coida_number', 'field_label' => 'COIDA/WCA Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'COIDA Assessment Notice', 'is_required' => 1],
            ['document_label' => 'Payroll Records / Earnings Breakdown', 'is_required' => 1],
            ['document_label' => 'Objection Letter', 'is_required' => 1],
            ['document_label' => 'Revised Assessment (if issued)', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 4. COIDA Employer Amendment
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'COIDA Employer Amendment',
        'description' => 'Update employer details with the Compensation Fund (address, contact, banking, classification)',
        'submission_to' => 'Dept of Labour / Compensation Fund',
        'steps' => [
            ['step_name' => 'Identify changes to employer details', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect supporting documents for changes', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Complete amendment form on CompEasy portal', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Upload supporting documents', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit amendment', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor amendment status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download updated registration details', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'wca_coida_number', 'field_label' => 'COIDA/WCA Number', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Supporting Documents for Amendment', 'is_required' => 1],
            ['document_label' => 'Updated Registration Details', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 5. Workplace Skills Plan (WSP) & Annual Training Report (ATR)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Workplace Skills Plan (WSP) & ATR',
        'description' => 'Submit Workplace Skills Plan and Annual Training Report to relevant SETA',
        'submission_to' => 'SETA',
        'steps' => [
            ['step_name' => 'Verify employer SETA registration', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Identify relevant SETA for the industry', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect training records for the past year (ATR)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect planned training details for next year (WSP)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Compile employee list with demographics', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete WSP & ATR forms on SETA portal', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain signature from Skills Development Facilitator (SDF)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to SETA by 30 April deadline', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Monitor mandatory grant approval', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive mandatory grant payment (20% of SDL)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'sdl_number', 'field_label' => 'SDL Number', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 0],
        ],
        'docs' => [
            ['document_label' => 'Employee List with Demographics', 'is_required' => 1],
            ['document_label' => 'Training Records (past year)', 'is_required' => 1],
            ['document_label' => 'Training Plan (next year)', 'is_required' => 1],
            ['document_label' => 'WSP & ATR Submission Confirmation', 'is_required' => 1],
            ['document_label' => 'Mandatory Grant Approval', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 6. Employment Equity (EE) Report
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Employment Equity (EE) Report',
        'description' => 'Prepare and submit Employment Equity Report to Dept of Employment and Labour (EEA2 / EEA4)',
        'submission_to' => 'Dept of Employment and Labour',
        'steps' => [
            ['step_name' => 'Verify employer is a designated employer (50+ employees or meets turnover threshold)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect workforce profile data (race, gender, disability)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect income differential data per occupational level', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Consult with EE Committee / employee representatives', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare EE Plan (if new or expired)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Complete EEA2 report (workforce profile)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete EEA4 report (income differentials)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit online via EE portal by 15 January deadline', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download submission confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Display EE summary in the workplace', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'dept_labour_number', 'field_label' => 'Dept of Labour Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 0],
        ],
        'docs' => [
            ['document_label' => 'Workforce Profile Data', 'is_required' => 1],
            ['document_label' => 'Income Differential Data', 'is_required' => 1],
            ['document_label' => 'EE Plan (if applicable)', 'is_required' => 0],
            ['document_label' => 'EEA2 / EEA4 Submission Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 7. UIF Benefits Claim (Employee Termination)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'UIF Benefits Claim (Employee)',
        'description' => 'Assist with UIF benefits claim for terminated/retrenched employee',
        'submission_to' => 'Dept of Labour (uFiling)',
        'steps' => [
            ['step_name' => 'Confirm reason for termination (retrenchment, dismissal, contract end, resignation)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect employee ID copy', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect last 6 months payslips', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect UI-19 declarations (last 4 filings)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Complete employer declaration on uFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Generate and print UI-19 discharge certificate', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Provide employee with UI-19, payslips, and ID copy', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advise employee to visit Labour Centre to claim', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Employer Name', 'is_required' => 1],
            ['field_name' => 'uif_number', 'field_label' => 'UIF Number', 'is_required' => 1],
            ['field_name' => 'dept_labour_number', 'field_label' => 'Dept of Labour Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Employee ID Copy', 'is_required' => 1],
            ['document_label' => 'Last 6 Months Payslips', 'is_required' => 1],
            ['document_label' => 'UI-19 Discharge Certificate', 'is_required' => 1],
            ['document_label' => 'Termination Letter / Notice', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 8. Bargaining Council Registration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Bargaining Council Registration',
        'description' => 'Register employer with relevant Bargaining Council and manage compliance',
        'submission_to' => 'Bargaining Council',
        'steps' => [
            ['step_name' => 'Identify applicable Bargaining Council for the industry', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify employer falls within scope of Bargaining Council', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect company registration documents', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect employee list with details', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Complete registration forms', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to Bargaining Council', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay registration and membership fees', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive registration confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Set up monthly contribution schedule', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advise client on BC wage rates and conditions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 0],
        ],
        'docs' => [
            ['document_label' => 'COR Certificate', 'is_required' => 1],
            ['document_label' => 'Employee List', 'is_required' => 1],
            ['document_label' => 'BC Registration Confirmation', 'is_required' => 1],
            ['document_label' => 'BC Wage Table / Agreement', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 9. COIDA Deregistration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'COIDA Deregistration',
        'description' => 'Deregister employer from the Compensation Fund (no employees / ceased trading)',
        'submission_to' => 'Dept of Labour / Compensation Fund',
        'steps' => [
            ['step_name' => 'Confirm employer has no employees', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit final Return of Earnings (W.As.8)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay any outstanding assessments', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete deregistration request on CompEasy', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit deregistration to Compensation Fund', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor deregistration status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download deregistration confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'wca_coida_number', 'field_label' => 'COIDA/WCA Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Final Return of Earnings', 'is_required' => 1],
            ['document_label' => 'Assessment Payment Receipts', 'is_required' => 1],
            ['document_label' => 'COIDA Deregistration Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 10. SETA Registration / SDF Appointment
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'SETA Registration / SDF Appointment',
        'description' => 'Register employer with relevant SETA and appoint Skills Development Facilitator',
        'submission_to' => 'SETA',
        'steps' => [
            ['step_name' => 'Identify correct SETA based on main business activity (SIC code)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify employer pays SDL (PAYE registered)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect company registration documents', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Register on SETA online portal', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Appoint Skills Development Facilitator (SDF)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete SDF appointment form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit SDF appointment to SETA', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive SETA registration confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Advise client on WSP/ATR submission requirements', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'sdl_number', 'field_label' => 'SDL Number', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'COR Certificate', 'is_required' => 1],
            ['document_label' => 'SDF Appointment Letter', 'is_required' => 1],
            ['document_label' => 'SETA Registration Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // OUTPUT
    // =====================================================
    echo "<h2 style='color:#28a745;'>COIDA / WCA / Labour Seed Data Complete!</h2>";
    echo "<p><strong>Additional Job Types Created (" . count($seeded) . "):</strong></p><ol>";
    foreach ($seeded as $s) {
        echo "<li>" . htmlspecialchars($s) . "</li>";
    }
    echo "</ol>";

    $totalTypes = DB::table('job_card_types')->count();
    echo "<p><strong>Total job types in system: " . $totalTypes . "</strong></p>";

    echo "<h3>All COIDA / WCA / Labour / SETA Job Types:</h3><ol>";
    $labourTypes = DB::table('job_card_types')
        ->where(function($q) {
            $q->where('submission_to', 'LIKE', '%Labour%')
              ->orWhere('submission_to', 'LIKE', '%Compensation%')
              ->orWhere('submission_to', 'LIKE', '%uFiling%')
              ->orWhere('submission_to', 'LIKE', '%SETA%')
              ->orWhere('submission_to', 'LIKE', '%Bargaining%');
        })
        ->orderBy('display_order')->get();
    foreach ($labourTypes as $t) {
        $stepCount = DB::table('job_card_type_steps')->where('job_type_id', $t->id)->count();
        echo "<li><strong>" . htmlspecialchars($t->name) . "</strong> (" . htmlspecialchars($t->submission_to) . ") — " . $stepCount . " steps</li>";
    }
    echo "</ol>";

    echo "<p>Go to <a href='/job-cards/admin/types'>Job Card Setup</a> to review all types!</p>";

} catch (\Exception $e) {
    echo "<h2 style='color:#dc3545;'>Seed Error</h2>";
    echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
