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
    // Get next display_order
    $maxOrder = DB::table('job_card_types')->max('display_order') ?? 0;
    $order = $maxOrder + 1;
    $seeded = [];

    // =====================================================
    // Company Reinstatement
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CIPC Company Reinstatement',
        'description' => 'Re-registration / reinstatement of a deregistered company or CC with CIPC',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Confirm company was deregistered (check CIPC status)', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Determine grounds for reinstatement', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect director/member ID copies', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare CoR40.5 reinstatement application', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare motivation letter for reinstatement', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit application to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay reinstatement fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay all outstanding annual return fees', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor reinstatement status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download reinstated COR certificate', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'File outstanding CIPC annual returns', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Update SARS registrations if needed', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'Director/Member First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Director/Member Surname', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'Director/Member ID Number', 'is_required' => 1],
            ['field_name' => 'bizportal_number', 'field_label' => 'BizPortal Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_mobile', 'field_label' => 'Mobile', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'CoR40.5 Reinstatement Application', 'is_required' => 1],
            ['document_label' => 'Motivation Letter', 'is_required' => 1],
            ['document_label' => 'Director/Member ID Copies', 'is_required' => 1],
            ['document_label' => 'Old COR Certificate (if available)', 'is_required' => 0],
            ['document_label' => 'Payment Receipts (fees & outstanding ARs)', 'is_required' => 1],
            ['document_label' => 'Reinstated COR Certificate', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // CIPC Name Change
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CIPC Company Name Change',
        'description' => 'Change company or CC name via CIPC',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Client to confirm proposed new name(s)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Perform CIPC name search (check availability)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit name reservation (CoR9.1)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay name reservation fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download name reservation confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare special resolution for name change (CoR15.2)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain directors/members signature on resolution', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Submit CoR15.2 and resolution to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay name change filing fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor name change status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download updated COR certificate', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Notify SARS of name change', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Notify bank of name change', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send updated documents to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Current Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'number_of_directors', 'field_label' => 'Number of Directors', 'is_required' => 1],
            ['field_name' => 'bizportal_number', 'field_label' => 'BizPortal Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Name Reservation Confirmation (CoR9.1)', 'is_required' => 1],
            ['document_label' => 'Signed Special Resolution', 'is_required' => 1],
            ['document_label' => 'CoR15.2 Filing', 'is_required' => 1],
            ['document_label' => 'Updated COR Certificate', 'is_required' => 1],
            ['document_label' => 'Payment Receipts', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // Share Transfer / Change of Shareholders
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CIPC Share Transfer / Shareholder Change',
        'description' => 'Transfer of shares and change of shareholders/members',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Verify current share register / member details', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Confirm details of share transfer (buyer/seller)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect buyer ID copy', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect seller ID copy', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare share transfer agreement / cession', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain signatures on transfer agreement', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update share register / members register', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare CoR39 (change of directors if needed)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Submit changes to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay filing fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download updated CIPC records', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Issue new share certificates (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Advise client re: CGT implications', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'number_of_shares', 'field_label' => 'Total Number of Shares', 'is_required' => 1],
            ['field_name' => 'number_of_directors', 'field_label' => 'Number of Directors', 'is_required' => 0],
            ['field_name' => 'director_first_name', 'field_label' => 'Director First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Director Surname', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Buyer ID Copy', 'is_required' => 1],
            ['document_label' => 'Seller ID Copy', 'is_required' => 1],
            ['document_label' => 'Share Transfer Agreement / Cession', 'is_required' => 1],
            ['document_label' => 'Updated Share Register', 'is_required' => 1],
            ['document_label' => 'Share Certificates (new)', 'is_required' => 0],
            ['document_label' => 'Updated CIPC Records', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // MOI Amendment
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CIPC MOI Amendment',
        'description' => 'Amendment of Memorandum of Incorporation (MOI) filed with CIPC',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Review current MOI', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Identify clauses to be amended', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Draft amended MOI / amendment schedule', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Prepare special resolution for MOI amendment', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain directors/shareholders signatures', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Complete CoR15.2 form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit CoR15.2 and amended MOI to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay filing fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor amendment status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download confirmation from CIPC', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send updated MOI to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'number_of_directors', 'field_label' => 'Number of Directors', 'is_required' => 1],
            ['field_name' => 'number_of_shares', 'field_label' => 'Number of Shares', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Current MOI', 'is_required' => 1],
            ['document_label' => 'Amended MOI / Amendment Schedule', 'is_required' => 1],
            ['document_label' => 'Signed Special Resolution', 'is_required' => 1],
            ['document_label' => 'CoR15.2 Filing Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // Beneficial Ownership Declaration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CIPC Beneficial Ownership Declaration',
        'description' => 'Filing of Beneficial Ownership (BO) declaration as required by CIPC (GN 1529 / Companies Amendment Act)',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Identify all beneficial owners (25%+ interest or significant control)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect beneficial owner ID copies', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Collect beneficial owner proof of address', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Determine nature and extent of beneficial interest', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete BO declaration on CIPC portal / BizPortal', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Upload supporting documents', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit declaration to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download filing confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update company BO register (internal)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'number_of_directors', 'field_label' => 'Number of Directors', 'is_required' => 1],
            ['field_name' => 'number_of_shares', 'field_label' => 'Number of Shares', 'is_required' => 0],
            ['field_name' => 'director_first_name', 'field_label' => 'BO First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'BO Surname', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'BO ID Number', 'is_required' => 1],
            ['field_name' => 'bizportal_number', 'field_label' => 'BizPortal Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Beneficial Owner ID Copies', 'is_required' => 1],
            ['document_label' => 'Beneficial Owner Proof of Address', 'is_required' => 1],
            ['document_label' => 'Share Register / Shareholding Schedule', 'is_required' => 1],
            ['document_label' => 'BO Declaration Filing Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // Non-Profit Company (NPC) Registration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CIPC Non-Profit Company (NPC) Registration',
        'description' => 'Register a new Non-Profit Company (NPC) with CIPC',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Confirm NPC objectives and public benefit activity', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Perform name reservation on CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Collect director/incorporator ID copies (min 3 directors)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Draft NPC MOI (must include s30 requirements)', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete CoR14.1 incorporation form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay registration fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor registration status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download COR certificate (NPC)', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Register for income tax with SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Apply for PBO / s18A status (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Create client record in CIMS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send all documents to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'company_name', 'field_label' => 'Proposed NPC Name', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'Director 1 First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'Director 1 Surname', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'Director 1 ID Number', 'is_required' => 1],
            ['field_name' => 'number_of_directors', 'field_label' => 'Number of Directors', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_mobile', 'field_label' => 'Mobile', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Director ID Copies (all)', 'is_required' => 1],
            ['document_label' => 'Name Reservation Confirmation', 'is_required' => 1],
            ['document_label' => 'NPC MOI (signed)', 'is_required' => 1],
            ['document_label' => 'CoR14.1 Incorporation Form', 'is_required' => 1],
            ['document_label' => 'COR Certificate (NPC)', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // External Company Registration
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CIPC External Company Registration',
        'description' => 'Register an external (foreign) company with CIPC to conduct business in South Africa',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Obtain foreign company registration documents', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Obtain apostilled/authenticated documents', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Verify South African representative details', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Prepare CoR20.1 external company registration', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay registration fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor registration status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download registration certificate', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Register for income tax, VAT etc. with SARS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Create client record in CIMS', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send all documents to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'company_name', 'field_label' => 'Foreign Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Foreign Registration Number', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'director_first_name', 'field_label' => 'SA Representative First Name', 'is_required' => 1],
            ['field_name' => 'director_surname', 'field_label' => 'SA Representative Surname', 'is_required' => 1],
            ['field_name' => 'director_id_number', 'field_label' => 'SA Representative ID/Passport', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
            ['field_name' => 'phone_business', 'field_label' => 'Business Phone', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Foreign Company Registration Documents', 'is_required' => 1],
            ['document_label' => 'Apostilled / Authenticated Documents', 'is_required' => 1],
            ['document_label' => 'SA Representative ID Copy', 'is_required' => 1],
            ['document_label' => 'CoR20.1 Form', 'is_required' => 1],
            ['document_label' => 'CIPC Registration Certificate', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // Increase / Decrease of Share Capital
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CIPC Increase / Decrease Share Capital',
        'description' => 'Increase or decrease of authorised share capital via MOI amendment',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Review current MOI for share capital provisions', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Determine new share capital structure', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Draft special resolution for share capital change', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain directors/shareholders signatures', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Amend MOI to reflect new share capital', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Complete CoR15.2 form', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay filing fee', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Monitor amendment status', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download confirmation from CIPC', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Update share register', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Issue new share certificates (if applicable)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Update client master', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'number_of_shares', 'field_label' => 'Current Number of Shares', 'is_required' => 1],
            ['field_name' => 'number_of_directors', 'field_label' => 'Number of Directors', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Current MOI', 'is_required' => 1],
            ['document_label' => 'Signed Special Resolution', 'is_required' => 1],
            ['document_label' => 'Amended MOI', 'is_required' => 1],
            ['document_label' => 'CoR15.2 Filing Confirmation', 'is_required' => 1],
            ['document_label' => 'Updated Share Register', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // Filing AFS with CIPC
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CIPC Filing of Annual Financial Statements',
        'description' => 'Filing of annual financial statements with CIPC (required for public companies and state-owned companies)',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Confirm company is required to file AFS with CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Obtain signed AFS from auditors/accountants', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Prepare XBRL tagged AFS (if required)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Log into CIPC filing system', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Upload AFS to CIPC portal', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit to CIPC', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download filing confirmation', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'financial_year_end', 'field_label' => 'Financial Year End', 'is_required' => 1],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'Signed Annual Financial Statements', 'is_required' => 1],
            ['document_label' => 'Audit / Review Report', 'is_required' => 0],
            ['document_label' => 'CIPC Filing Confirmation', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // CIPC Compliance Status Remediation
    // =====================================================
    $seeded[] = seedJobType([
        'name' => 'CIPC Compliance Status Remediation',
        'description' => 'Bring company back into good standing - file outstanding annual returns, clear compliance issues',
        'submission_to' => 'CIPC',
        'steps' => [
            ['step_name' => 'Run CIPC company search and review status', 'step_type' => 'info_review', 'is_required' => 1],
            ['step_name' => 'Identify all outstanding annual returns', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Identify any compliance notices or penalties', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify director details are current', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Verify registered address is current', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all outstanding annual returns', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Pay all outstanding fees and penalties', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Submit any required amendments', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'File Beneficial Ownership declaration (if outstanding)', 'step_type' => 'checkbox', 'is_required' => 0],
            ['step_name' => 'Verify company status changed to "In Good Standing"', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'Download updated CIPC records / compliance certificate', 'step_type' => 'document_required', 'is_required' => 1],
            ['step_name' => 'Send confirmation to client', 'step_type' => 'checkbox', 'is_required' => 1],
            ['step_name' => 'File all documents', 'step_type' => 'checkbox', 'is_required' => 1],
        ],
        'fields' => [
            ['field_name' => 'client_code', 'field_label' => 'Client Code', 'is_required' => 1],
            ['field_name' => 'company_name', 'field_label' => 'Company Name', 'is_required' => 1],
            ['field_name' => 'company_reg_number', 'field_label' => 'Registration Number', 'is_required' => 1],
            ['field_name' => 'company_type', 'field_label' => 'Company Type', 'is_required' => 1],
            ['field_name' => 'cipc_annual_returns', 'field_label' => 'CIPC Annual Returns Status', 'is_required' => 1],
            ['field_name' => 'number_of_directors', 'field_label' => 'Number of Directors', 'is_required' => 0],
            ['field_name' => 'bizportal_number', 'field_label' => 'BizPortal Number', 'is_required' => 0],
            ['field_name' => 'email', 'field_label' => 'Email', 'is_required' => 1],
        ],
        'docs' => [
            ['document_label' => 'CIPC Company Search Report', 'is_required' => 1],
            ['document_label' => 'Annual Return Receipts', 'is_required' => 1],
            ['document_label' => 'Payment Receipts', 'is_required' => 1],
            ['document_label' => 'Updated CIPC Records / Compliance Certificate', 'is_required' => 1],
        ],
    ], $order++);

    // =====================================================
    // OUTPUT
    // =====================================================
    echo "<h2 style='color:#28a745;'>CIPC Seed Data Complete!</h2>";
    echo "<p><strong>Additional CIPC Job Types Created (" . count($seeded) . "):</strong></p><ol>";
    foreach ($seeded as $s) {
        echo "<li>" . htmlspecialchars($s) . "</li>";
    }
    echo "</ol>";

    $totalTypes = DB::table('job_card_types')->count();
    echo "<p><strong>Total job types in system: " . $totalTypes . "</strong></p>";

    echo "<h3>All CIPC Job Types:</h3><ol>";
    $cipcTypes = DB::table('job_card_types')->where('submission_to', 'CIPC')->orderBy('display_order')->get();
    foreach ($cipcTypes as $t) {
        $stepCount = DB::table('job_card_type_steps')->where('job_type_id', $t->id)->count();
        echo "<li><strong>" . htmlspecialchars($t->name) . "</strong> — " . $stepCount . " steps</li>";
    }
    echo "</ol>";

    echo "<p>Go to <a href='/job-cards/admin/types'>Job Card Setup</a> to review all types!</p>";

} catch (\Exception $e) {
    echo "<h2 style='color:#dc3545;'>Seed Error</h2>";
    echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
