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
    // 1. CCMA Dispute / Conciliation & Arbitration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CCMA Dispute - Conciliation & Arbitration',
        'description' => 'Represent employer in CCMA conciliation and arbitration proceedings (unfair dismissal, unfair labour practice)',
        'submission_to' => 'CCMA',
        'steps' => [
            ['step_name' => 'Receive CCMA referral notice (LRA 7.11)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Identify nature of dispute (unfair dismissal / ULP / wage dispute)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect employment contract', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect disciplinary hearing records (if dismissal)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Collect employee personnel file', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare employer response / statement of case', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Attend conciliation hearing', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'If unresolved - receive certificate of non-resolution', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Prepare for arbitration (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Attend arbitration hearing', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Receive CCMA award / settlement agreement', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Advise client on compliance with award', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Consider review (Labour Court) if award unfavourable', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Employer Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'CCMA Referral Notice (LRA 7.11)', 'is_required' => 1],
            ['document_label' => 'Employment Contract', 'is_required' => 1],
            ['document_label' => 'Employee Personnel File', 'is_required' => 1],
            ['document_label' => 'Disciplinary Hearing Records', 'is_required' => 0],
            ['document_label' => 'Employer Statement of Case', 'is_required' => 1],
            ['document_label' => 'Certificate of Non-Resolution', 'is_required' => 0],
            ['document_label' => 'CCMA Award / Settlement Agreement', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 2. Retrenchment Process (s189 / s189A)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Retrenchment Process (s189 / s189A)',
        'description' => 'Manage the retrenchment / operational requirements process in compliance with LRA s189',
        'submission_to' => 'Dept of Employment and Labour / CCMA',
        'steps' => [
            ['step_name' => 'Verify operational requirements justification', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Determine if s189 (small scale) or s189A (50+ employees) applies', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare s189(3) notice / letter to employees', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Issue s189(3) notice to affected employees and unions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Notify Dept of Employment and Labour (if 50+ employees)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Conduct consultation meetings with employees/union', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Consider alternatives to retrenchment', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Apply selection criteria (LIFO or agreed criteria)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate severance pay (1 week per completed year)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate outstanding leave pay', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Calculate notice period pay', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare retrenchment letters for affected employees', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Issue retrenchment letters', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Process final payroll and severance payments', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete UIF discharge on uFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Issue certificates of service', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 1],
            ['field_name' => 'uif_number', 'field_label' => 'UIF Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 's189(3) Notice to Employees', 'is_required' => 1],
            ['document_label' => 'Consultation Meeting Minutes', 'is_required' => 1],
            ['document_label' => 'Selection Criteria Document', 'is_required' => 1],
            ['document_label' => 'Severance Calculation Schedule', 'is_required' => 1],
            ['document_label' => 'Retrenchment Letters', 'is_required' => 1],
            ['document_label' => 'Final Payroll / Severance Payments', 'is_required' => 1],
            ['document_label' => 'Certificates of Service', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 3. Employment Contract Preparation
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Employment Contract Preparation',
        'description' => 'Draft or review employment contract compliant with BCEA and relevant sectoral determination',
        'submission_to' => 'Internal / Client',
        'steps' => [
            ['step_name' => 'Confirm position details (title, duties, hours, remuneration)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Determine applicable sectoral determination or Bargaining Council agreement', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify minimum wage compliance', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Draft employment contract (BCEA compliant)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Include required clauses: hours of work, leave, notice period, deductions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Include restraint of trade / confidentiality (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Internal review of contract', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send draft to client for review', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Address client queries / amendments', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Finalize contract', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain signatures (employer and employee)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Provide copy to employee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Signed Employment Contract', 'is_required' => 1],
            ['document_label' => 'Employee ID Copy', 'is_required' => 1],
            ['document_label' => 'Job Description', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 4. Disciplinary Procedure
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Disciplinary Procedure',
        'description' => 'Manage internal disciplinary hearing process (warnings, dismissal) in compliance with LRA Schedule 8',
        'submission_to' => 'Internal / Client',
        'steps' => [
            ['step_name' => 'Investigate alleged misconduct / poor performance', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect evidence and witness statements', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Determine if formal hearing is warranted', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare notice to attend disciplinary hearing', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Issue notice to employee (minimum 48hrs before hearing)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Appoint chairperson (independent if possible)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Conduct disciplinary hearing', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Record hearing minutes / proceedings', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Chairperson to make finding and recommendation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Issue outcome letter (warning / final warning / dismissal)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advise employee of right to appeal / refer to CCMA', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Process appeal (if lodged)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'If dismissal - process final payroll and UIF discharge', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'File all documents in employee file', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 0],
        ],
        'docs' => [
            ['document_label' => 'Investigation Report / Evidence', 'is_required' => 1],
            ['document_label' => 'Notice to Attend Hearing', 'is_required' => 1],
            ['document_label' => 'Hearing Minutes / Proceedings', 'is_required' => 1],
            ['document_label' => 'Outcome Letter (Warning / Dismissal)', 'is_required' => 1],
            ['document_label' => 'Employee Acknowledgement of Receipt', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 5. UIF Maternity Benefits Claim
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'UIF Maternity Benefits Claim',
        'description' => 'Assist employee with UIF maternity benefits claim',
        'submission_to' => 'Dept of Labour (uFiling)',
        'steps' => [
            ['step_name' => 'Verify employee UIF contributions are up to date', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Confirm expected date of confinement', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect employee ID copy', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect maternity certificate from doctor / midwife', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect last 6 months payslips', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Complete employer declaration on uFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Generate UI-4 form (application for benefits)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Provide employee with documents for Labour Centre', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Employee to submit at Labour Centre', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Employer Name', 'is_required' => 1],
            ['field_name' => 'uif_number', 'field_label' => 'UIF Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Employee ID Copy', 'is_required' => 1],
            ['document_label' => 'Maternity Certificate (doctor/midwife)', 'is_required' => 1],
            ['document_label' => 'Last 6 Months Payslips', 'is_required' => 1],
            ['document_label' => 'UI-4 Application for Benefits', 'is_required' => 1],
            ['document_label' => 'Bank Confirmation Letter', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 6. UIF Illness Benefits Claim
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'UIF Illness Benefits Claim',
        'description' => 'Assist employee with UIF illness benefits claim (off work for 14+ days)',
        'submission_to' => 'Dept of Labour (uFiling)',
        'steps' => [
            ['step_name' => 'Verify employee has been off work for 14+ consecutive days', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify employee UIF contributions are up to date', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect employee ID copy', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect medical certificate from doctor', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect last 6 months payslips', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Complete employer declaration on uFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Generate UI-3 form (sick leave application)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Provide employee with documents for Labour Centre', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Employee to submit at Labour Centre', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Employer Name', 'is_required' => 1],
            ['field_name' => 'uif_number', 'field_label' => 'UIF Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Employee ID Copy', 'is_required' => 1],
            ['document_label' => 'Medical Certificate', 'is_required' => 1],
            ['document_label' => 'Last 6 Months Payslips', 'is_required' => 1],
            ['document_label' => 'UI-3 Illness Benefits Application', 'is_required' => 1],
            ['document_label' => 'Bank Confirmation Letter', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 7. OHS Act Compliance & Registration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'OHS Act Compliance & Registration',
        'description' => 'Occupational Health and Safety Act compliance — appoint s16 assignee, health & safety representatives',
        'submission_to' => 'Dept of Employment and Labour',
        'steps' => [
            ['step_name' => 'Determine OHS Act obligations based on industry and employee count', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Appoint s16(2) CEO / Managing Director as responsible person', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Appoint s16(1) assignee (if delegating OHS duties)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Elect / appoint Health & Safety Representatives (20+ employees)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Establish Health & Safety Committee (if required)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Prepare OHS Policy document', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Conduct risk assessment of workplace', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Set up incident / accident register', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Ensure first aid equipment and trained first aider', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Display required notices (OHS Act, employer name, etc.)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send OHS compliance file to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'CEO / MD First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'CEO / MD Surname', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 0],
        ],
        'docs' => [
            ['document_label' => 's16(2) Appointment Letter', 'is_required' => 1],
            ['document_label' => 's16(1) Assignee Appointment (if applicable)', 'is_required' => 0],
            ['document_label' => 'OHS Policy Document', 'is_required' => 1],
            ['document_label' => 'Risk Assessment Report', 'is_required' => 1],
            ['document_label' => 'H&S Representative Nomination', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 8. Labour Inspector Audit / Investigation Response
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Labour Inspector Audit Response',
        'description' => 'Respond to Dept of Employment and Labour inspection / audit on compliance with BCEA, NMW, OHS, EE',
        'submission_to' => 'Dept of Employment and Labour',
        'steps' => [
            ['step_name' => 'Review labour inspector notice / compliance order', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Identify areas of alleged non-compliance', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect all employment records (contracts, payslips, leave records)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect time and attendance records', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Review wage compliance against BCEA / NMW / sectoral determination', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review leave records compliance', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare response to inspector with supporting documents', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Attend meeting with labour inspector (if required)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Rectify any genuine non-compliance issues', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit response and evidence to inspector', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive outcome / compliance certificate', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Object to compliance order (if unfair)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Advise client on outcome', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
            ['field_name' => 'dept_labour_number', 'field_label' => 'Dept of Labour Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Labour Inspector Notice / Compliance Order', 'is_required' => 1],
            ['document_label' => 'Employment Contracts', 'is_required' => 1],
            ['document_label' => 'Payslips / Payroll Records', 'is_required' => 1],
            ['document_label' => 'Time & Attendance Records', 'is_required' => 1],
            ['document_label' => 'Leave Records', 'is_required' => 1],
            ['document_label' => 'Response Letter to Inspector', 'is_required' => 1],
            ['document_label' => 'Outcome / Compliance Certificate', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 9. Section 197 Business Transfer
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Section 197 Business Transfer',
        'description' => 'Transfer of employees as a going concern (s197 LRA) — old employer to new employer',
        'submission_to' => 'Internal / Client',
        'steps' => [
            ['step_name' => 'Verify transaction qualifies as s197 transfer', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Identify all affected employees', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Compile employee details (contracts, terms, benefits)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Notify affected employees of transfer', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Consult with employees / union on transfer terms', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare s197 transfer agreement between old and new employer', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Agree on transfer of leave balances and accruals', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Agree on transfer of provident/pension fund membership', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Old employer to deregister PAYE (if closing)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'New employer to register PAYE (if new)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Transfer UIF records on uFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Issue new employment contracts (terms no less favourable)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Old Employer Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Old Employer Reg Number', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 1],
            ['field_name' => 'uif_number', 'field_label' => 'UIF Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 's197 Transfer Agreement', 'is_required' => 1],
            ['document_label' => 'Employee List with Terms', 'is_required' => 1],
            ['document_label' => 'Employee Notification Letters', 'is_required' => 1],
            ['document_label' => 'Consultation Meeting Minutes', 'is_required' => 1],
            ['document_label' => 'New Employment Contracts', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 10. National Minimum Wage Exemption Application
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'National Minimum Wage Exemption',
        'description' => 'Apply for exemption from National Minimum Wage (NMW) for financially distressed employer',
        'submission_to' => 'Dept of Employment and Labour',
        'steps' => [
            ['step_name' => 'Verify employer cannot afford to pay NMW', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare financial evidence of inability to pay', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Consult with employees / union on proposed exemption', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain employee / union written response', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Complete NMW exemption application form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect supporting financial statements', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Submit application to Dept of Employment and Labour', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor application status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive exemption decision', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Advise client of outcome and conditions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Financial Statements / Management Accounts', 'is_required' => 1],
            ['document_label' => 'Employee / Union Consultation Response', 'is_required' => 1],
            ['document_label' => 'NMW Exemption Application Form', 'is_required' => 1],
            ['document_label' => 'Exemption Decision Letter', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // OUTPUT
    // =====================================================
    echo "<h2 style='color:#28a745;'>Dept of Labour Seed Data Complete!</h2>";
    echo "<p><strong>Additional Labour Job Types Created (" . count($seeded) . "):</strong></p><ol>";
    foreach ($seeded as $s) {
        echo "<li>" . htmlspecialchars($s) . "</li>";
    }
    echo "</ol>";

    $totalTypes = DB::table('job_card_types')->count();
    echo "<p><strong>Total job types in system: " . $totalTypes . "</strong></p>";

    echo "<h3>All Labour / Employment Related Job Types:</h3><ol>";
    $labourTypes = DB::table('job_card_types')
        ->where(function($q) {
            $q->where('submission_to', 'LIKE', '%Labour%')
              ->orWhere('submission_to', 'LIKE', '%Compensation%')
              ->orWhere('submission_to', 'LIKE', '%uFiling%')
              ->orWhere('submission_to', 'LIKE', '%SETA%')
              ->orWhere('submission_to', 'LIKE', '%Bargaining%')
              ->orWhere('submission_to', 'LIKE', '%CCMA%')
              ->orWhere('name', 'LIKE', '%UIF%')
              ->orWhere('name', 'LIKE', '%COIDA%')
              ->orWhere('name', 'LIKE', '%Payroll%')
              ->orWhere('name', 'LIKE', '%Employment%')
              ->orWhere('name', 'LIKE', '%Disciplin%')
              ->orWhere('name', 'LIKE', '%Retrench%')
              ->orWhere('name', 'LIKE', '%OHS%')
              ->orWhere('name', 'LIKE', '%PAYE%')
              ->orWhere('name', 'LIKE', '%EMP201%')
              ->orWhere('name', 'LIKE', '%EMP501%')
              ->orWhere('name', 'LIKE', '%ETI%');
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
