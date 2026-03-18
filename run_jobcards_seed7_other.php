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
    // 1. Client Onboarding / New Client Setup
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Client Onboarding / New Client Setup',
        'description' => 'Complete new client onboarding — engagement letter, FICA, eFiling linking, populate client master',
        'submission_to' => 'Internal',
        'steps' => [
            ['step_name' => 'Initial client meeting / consultation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Determine services required', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Perform FICA / KYC verification', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect FICA documents (ID, proof of address, COR)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare and issue engagement letter', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive signed engagement letter', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Create client record in CIMS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Populate all client master fields', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain SARS Power of Attorney', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Link client on SARS eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain previous accountant handover pack (if applicable)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Review SARS compliance status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review CIPC compliance status', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Set up client compliance calendar', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send welcome pack to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company / Client Name', 'is_required' => 1],
            ['field_name' => 'trading_name', 'field_label' => 'Trading Name', 'is_required' => 0],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 0],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'Director/Owner First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Director/Owner Surname', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'Director/Owner ID Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_mobile', 'field_label' => 'Mobile', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 0],
        ],
        'docs' => [
            ['document_label' => 'ID Copy (FICA)', 'is_required' => 1],
            ['document_label' => 'Proof of Address (FICA)', 'is_required' => 1],
            ['document_label' => 'COR Certificate', 'is_required' => 0],
            ['document_label' => 'Signed Engagement Letter', 'is_required' => 1],
            ['document_label' => 'SARS Power of Attorney', 'is_required' => 1],
            ['document_label' => 'Previous Accountant Handover Pack', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 2. Engagement Letter / Letter of Appointment
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Engagement Letter / Letter of Appointment',
        'description' => 'Prepare, issue and obtain signed engagement letter or letter of appointment for services',
        'submission_to' => 'Internal / Client',
        'steps' => [
            ['step_name' => 'Confirm scope of services to be rendered', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Agree on fee structure (fixed, hourly, retainer)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Draft engagement letter', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Include terms: scope, fees, responsibilities, limitations, FICA', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Internal review of engagement letter', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send to client for review', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Address client queries', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Obtain client signature', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Countersign and file', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Client Name', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'Contact First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Contact Surname', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Signed Engagement Letter', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 3. Management Accounts (Monthly / Quarterly)
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Management Accounts (Monthly / Quarterly)',
        'description' => 'Prepare monthly or quarterly management accounts and reporting pack for client',
        'submission_to' => 'Internal / Client',
        'steps' => [
            ['step_name' => 'Confirm bookkeeping is up to date for the period', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Process month-end adjustments (accruals, prepayments)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete bank reconciliation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Reconcile intercompany accounts (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Generate trial balance', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare Income Statement', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare Balance Sheet', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare Cash Flow Statement (if required)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Prepare variance analysis (actual vs budget)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Add commentary on key items', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Internal review', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send management pack to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Schedule review meeting with client (if required)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Trial Balance', 'is_required' => 1],
            ['document_label' => 'Income Statement', 'is_required' => 1],
            ['document_label' => 'Balance Sheet', 'is_required' => 1],
            ['document_label' => 'Bank Reconciliation', 'is_required' => 1],
            ['document_label' => 'Management Pack (full)', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 4. Budget & Cash Flow Forecast
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Budget & Cash Flow Forecast',
        'description' => 'Prepare annual budget, cash flow projections and financial forecasts for client',
        'submission_to' => 'Internal / Client',
        'steps' => [
            ['step_name' => 'Obtain prior year financial statements', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Meet with client to discuss business plans and targets', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Gather revenue assumptions and growth targets', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Gather expense assumptions (salaries, rent, utilities, etc.)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare income budget (monthly breakdown)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare expense budget (monthly breakdown)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare cash flow forecast (12 months)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Identify cash flow gaps and funding needs', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Internal review', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Present budget to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Address client queries and finalise', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Prior Year Financial Statements', 'is_required' => 1],
            ['document_label' => 'Budget Document', 'is_required' => 1],
            ['document_label' => 'Cash Flow Forecast', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 5. Year-End Close-Off Procedures
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Year-End Close-Off Procedures',
        'description' => 'Complete financial year-end close-off — final adjustments, provisions, schedules before AFS preparation',
        'submission_to' => 'Internal',
        'steps' => [
            ['step_name' => 'Final bank reconciliation for year-end', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Reconcile all debtors accounts', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Reconcile all creditors accounts', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Reconcile VAT account', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Reconcile PAYE / UIF / SDL payroll accounts', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Process depreciation entries', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review and update fixed asset register', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Process accruals and prepayments', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Process provisions (bad debts, leave pay, bonuses)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Process stock / inventory adjustment (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Reconcile loan accounts', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Reconcile directors loan accounts', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Process tax provision (current + deferred)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Generate final adjusted trial balance', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare year-end working paper file', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
            ['field_name' => 'vat_number', 'field_label' => 'VAT Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Bank Reconciliation (year-end)', 'is_required' => 1],
            ['document_label' => 'Debtors / Creditors Reconciliation', 'is_required' => 1],
            ['document_label' => 'Fixed Asset Register', 'is_required' => 1],
            ['document_label' => 'Adjusted Trial Balance', 'is_required' => 1],
            ['document_label' => 'Year-End Working Paper File', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 6. Trust Amendment / Change of Trustees
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Trust Amendment / Change of Trustees',
        'description' => 'Amend trust deed or effect change of trustees with the Master of the High Court',
        'submission_to' => 'Master of High Court',
        'steps' => [
            ['step_name' => 'Review current trust deed and Letters of Authority', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Determine changes required (new trustee / resigned trustee / deed amendment)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect new trustee ID copy (if appointing)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Collect trustee acceptance form (new trustee)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Collect trustee resignation letter (if resigning)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Draft supplementary trust deed / amendment (if deed changes)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'All trustees to sign amendment / consent resolution', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare J401 / J516 form for Master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to Master of High Court', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay filing fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor processing by Master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Receive updated Letters of Authority', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update bank signatories (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send updated documents to trustees', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Trust Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Trust Number (IT)', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'Trustee First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Trustee Surname', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'Trustee ID Number', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Current Trust Deed', 'is_required' => 1],
            ['document_label' => 'Current Letters of Authority', 'is_required' => 1],
            ['document_label' => 'New Trustee ID Copy', 'is_required' => 0],
            ['document_label' => 'Trustee Acceptance / Resignation', 'is_required' => 1],
            ['document_label' => 'Supplementary Trust Deed / Amendment', 'is_required' => 0],
            ['document_label' => 'J401/J516 Form', 'is_required' => 1],
            ['document_label' => 'Updated Letters of Authority', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 7. Secretarial Compliance Calendar
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Secretarial Compliance Calendar',
        'description' => 'Annual compliance review — verify all statutory filings and registrations are current',
        'submission_to' => 'Internal',
        'steps' => [
            ['step_name' => 'Review CIPC annual return status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review SARS income tax return status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review VAT return status (if registered)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review PAYE / EMP201 submission status', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review EMP501 reconciliation status', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review provisional tax (IRP6) status', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review COIDA Return of Earnings status', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review UIF declarations status', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review B-BBEE certificate validity', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review Tax Clearance Certificate validity', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review CIPC Beneficial Ownership declaration', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Check director details are current on CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Check registered address is current', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare compliance summary report', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send compliance report to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Create job cards for outstanding items', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'vat_number', 'field_label' => 'VAT Number', 'is_required' => 0],
            ['field_name' => 'paye_number', 'field_label' => 'PAYE Number', 'is_required' => 0],
            ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Compliance Summary Report', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 8. Business Plan & Company Profile
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Business Plan & Company Profile',
        'description' => 'Prepare business plan, company profile or CSD profile for tenders and funding applications',
        'submission_to' => 'Internal / Client',
        'steps' => [
            ['step_name' => 'Client consultation on business objectives', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect company details and history', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect financial statements (last 3 years if available)', 'step_type' => 'document_required', 'is_required' => 0],
            ['step_name' => 'Research industry and market analysis', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Prepare executive summary', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare company overview and structure', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare products / services description', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare financial projections (3-5 years)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare SWOT analysis', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Internal review', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send draft to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Finalise and issue', 'step_type' => 'checkbox', 'is_required' => 1],
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
            ['document_label' => 'COR Certificate', 'is_required' => 0],
            ['document_label' => 'Financial Statements', 'is_required' => 0],
            ['document_label' => 'Business Plan Document', 'is_required' => 1],
            ['document_label' => 'Company Profile Document', 'is_required' => 0],
        ],
    ], $order++);

    // =====================================================
    // 9. Client Offboarding / File Handover
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Client Offboarding / File Handover',
        'description' => 'Close client relationship — compile handover pack, delink eFiling, transfer records',
        'submission_to' => 'Internal',
        'steps' => [
            ['step_name' => 'Confirm all outstanding work is completed', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Issue final invoice and receive payment', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Compile handover pack (AFS, returns, certificates, registrations)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Include SARS compliance history', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Include CIPC compliance history', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Delink client from SARS eFiling', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Revoke Power of Attorney with SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Transfer records to new accountant (if known)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Send handover pack to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Archive client file', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Mark client as inactive in CIMS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Client Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'sars_login', 'field_label' => 'SARS eFiling Login', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Handover Pack', 'is_required' => 1],
            ['document_label' => 'Final Invoice', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 10. Tax Planning & Advisory
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Tax Planning & Advisory',
        'description' => 'Annual or ad hoc tax planning session — structure review, tax optimisation, compliance advisory',
        'submission_to' => 'Internal / Client',
        'steps' => [
            ['step_name' => 'Review client current tax position', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Review latest financial statements / management accounts', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Identify tax saving opportunities', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review business structure for tax efficiency', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Consider s12J / s11D / other incentives (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review provisional tax estimates', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Review retirement fund contributions and deductions', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Prepare tax planning memo / report', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Present recommendations to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Implement agreed actions', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Client Name', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Financial Statements / Management Accounts', 'is_required' => 1],
            ['document_label' => 'Tax Planning Memo / Report', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 11. CSD (Central Supplier Database) Registration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CSD Registration (Central Supplier Database)',
        'description' => 'Register company on National Treasury Central Supplier Database for government tenders',
        'submission_to' => 'National Treasury',
        'steps' => [
            ['step_name' => 'Verify company has valid COR certificate', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify valid Tax Clearance Certificate / TCS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify valid B-BBEE certificate / affidavit', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect bank confirmation letter', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect director ID copies', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Register on CSD portal (www.csd.gov.za)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete company profile and commodity codes', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Upload all required documents', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit for verification', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor verification status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download CSD report / MAAA number', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send CSD details to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'tax_number', 'field_label' => 'Income Tax Number', 'is_required' => 1],
            ['field_name' => 'bank_name', 'field_label' => 'Bank Name', 'is_required' => 1],
            ['field_name' => 'bank_account_number', 'field_label' => 'Account Number', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'Director First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Director Surname', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'COR Certificate', 'is_required' => 1],
            ['document_label' => 'Tax Clearance Certificate / TCS', 'is_required' => 1],
            ['document_label' => 'B-BBEE Certificate / Affidavit', 'is_required' => 1],
            ['document_label' => 'Bank Confirmation Letter', 'is_required' => 1],
            ['document_label' => 'Director ID Copies', 'is_required' => 1],
            ['document_label' => 'CSD Report / MAAA Number', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // 12. Due Diligence / Business Valuation
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'Due Diligence / Business Valuation',
        'description' => 'Perform financial due diligence or business valuation for sale, acquisition or investment',
        'submission_to' => 'Internal / Client',
        'steps' => [
            ['step_name' => 'Define scope and purpose of due diligence / valuation', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect financial statements (last 3-5 years)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect management accounts (YTD)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect tax compliance records', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Review revenue trends and quality of earnings', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review asset register and valuations', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review liabilities (disclosed and contingent)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review contracts and commitments', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Review employment and labour compliance', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Perform valuation calculation (DCF / NAV / earnings multiple)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Identify risks and red flags', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare due diligence / valuation report', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Present findings to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Target Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Financial Statements (3-5 years)', 'is_required' => 1],
            ['document_label' => 'Management Accounts (YTD)', 'is_required' => 1],
            ['document_label' => 'Tax Compliance Records', 'is_required' => 1],
            ['document_label' => 'Due Diligence / Valuation Report', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // OUTPUT
    // =====================================================
    echo "<h2 style='color:#28a745;'>Other / Internal Seed Data Complete!</h2>";
    echo "<p><strong>Additional Job Types Created (" . count($seeded) . "):</strong></p><ol>";
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
