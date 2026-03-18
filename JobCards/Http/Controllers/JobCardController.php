<?php

namespace Modules\JobCards\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\JobCards\Services\JobCardService;
use Modules\JobCards\Services\PackGeneratorService;
use Modules\JobCards\Services\BODocumentService;
use Carbon\Carbon;

class JobCardController extends Controller
{
    protected JobCardService $service;
    protected PackGeneratorService $packService;
    protected BODocumentService $boService;

    public function __construct(JobCardService $service, PackGeneratorService $packService, BODocumentService $boService)
    {
        $this->service = $service;
        $this->packService = $packService;
        $this->boService = $boService;
    }

    /**
     * Dashboard — full page dedicated view.
     */
    public function dashboard()
    {
        $stats = $this->service->getDashboardStats();
        $jobTypes = $this->service->getJobTypes();
        $users = $this->service->getUsers();
        $statuses = config('job_cards.statuses');
        $priorities = config('job_cards.priorities');

        return view('jobcards::dashboard', compact('stats', 'jobTypes', 'users', 'statuses', 'priorities'));
    }

    /**
     * Dashboard stats API endpoint.
     */
    public function dashboardStats()
    {
        return response()->json($this->service->getDashboardStats());
    }

    /**
     * Job cards list.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'job_type_id', 'assigned_to', 'search']);
        $result = $this->service->getJobCards($filters);
        $jobTypes = $this->service->getJobTypes();
        $users = $this->service->getUsers();
        $statuses = config('job_cards.statuses');

        return view('jobcards::index', array_merge($result, [
            'jobTypes'  => $jobTypes,
            'users'     => $users,
            'statuses'  => $statuses,
            'filters'   => $filters,
        ]));
    }

    /**
     * Create job card form.
     */
    public function create()
    {
        $jobTypes = $this->service->getJobTypes();
        $users = $this->service->getUsers();
        $clients = $this->service->searchClients('', 500);
        $priorities = config('job_cards.priorities');
        $statuses = config('job_cards.statuses');

        return view('jobcards::create', compact('jobTypes', 'users', 'clients', 'priorities', 'statuses'));
    }

    /**
     * Store new job card.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id'     => 'required|integer',
            'job_type_id'   => 'required|integer|exists:cims_job_card_types,id',
            'status'        => 'nullable|in:draft,in_progress,review,completed,submitted,cancelled',
            'priority'      => 'nullable|in:low,normal,high,urgent',
            'due_date'      => 'nullable|date',
            'assigned_to'   => 'nullable|integer',
            'followed_by'   => 'nullable|integer',
            'notes'         => 'nullable|string',
        ]);

        $jobCardId = $this->service->createJobCard($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'id' => $jobCardId]);
        }

        return redirect()->route('jobcards.show', $jobCardId)
            ->with('success', 'Job card created successfully.');
    }

    /**
     * Show job card detail page.
     */
    public function show(int $id)
    {
        $data = $this->service->getJobCard($id);
        if (!$data) abort(404);

        $statuses = config('job_cards.statuses');
        $priorities = config('job_cards.priorities');
        $users = $this->service->getUsers();

        return view('jobcards::show', array_merge($data, [
            'statuses'   => $statuses,
            'priorities' => $priorities,
            'users'      => $users,
        ]));
    }

    /**
     * Update job card.
     */
    public function update(Request $request, int $id)
    {
        $this->service->updateJobCard($id, $request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('jobcards.show', $id)
            ->with('success', 'Job card updated.');
    }

    /**
     * Soft-delete job card.
     */
    public function destroy(int $id)
    {
        $this->service->deleteJobCard($id);
        return redirect()->route('jobcards.index')
            ->with('success', 'Job card deleted.');
    }

    /**
     * AJAX: Update a step status.
     */
    public function updateStep(Request $request, int $id)
    {
        $request->validate([
            'step_id' => 'required|integer',
            'status'  => 'required|in:pending,in_progress,completed,skipped,na',
        ]);

        $result = $this->service->updateStepStatus(
            $id,
            $request->input('step_id'),
            $request->input('status'),
            $request->input('notes')
        );

        return response()->json(['success' => true] + $result);
    }

    /**
     * AJAX: Update job card status.
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:draft,in_progress,review,completed,submitted,cancelled',
        ]);

        $this->service->updateJobCard($id, ['status' => $request->input('status')]);

        return response()->json(['success' => true]);
    }

    /**
     * Upload document to a job card.
     */
    public function uploadDocument(Request $request, int $id)
    {
        $request->validate([
            'document' => 'required|file|max:20480',
            'step_id'  => 'nullable|integer',
        ]);

        $file = $request->file('document');
        $storagePath = base_path('../storage/' . config('job_cards.documents_path', 'job_cards/documents'));
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $storedName = time() . '_' . $file->getClientOriginalName();
        $file->move($storagePath, $storedName);

        DB::table('cims_job_card_attachments')->insert([
            'job_card_id'        => $id,
            'step_id'            => $request->input('step_id'),
            'file_name'          => $storedName,
            'file_original_name' => $file->getClientOriginalName(),
            'file_path'          => config('job_cards.documents_path', 'job_cards/documents') . '/' . $storedName,
            'file_mime_type'     => $file->getClientMimeType(),
            'file_size'          => $file->getSize(),
            'file_type'          => 'source_document',
            'uploaded_by'        => Auth::id(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // If linked to a step with type document_required, auto-complete that step
        if ($request->input('step_id')) {
            $step = DB::table('cims_job_card_type_steps')
                ->where('id', $request->input('step_id'))
                ->first();
            if ($step && $step->step_type === 'document_required') {
                $this->service->updateStepStatus($id, $request->input('step_id'), 'completed');
            }
        }

        return response()->json(['success' => true, 'message' => 'Document uploaded successfully.']);
    }

    /**
     * Generate PDF pack.
     */
    public function generatePack(Request $request, int $id)
    {
        $type = $request->input('type', 'internal');
        $data = $this->service->getJobCard($id);
        if (!$data) return response()->json(['success' => false, 'message' => 'Job card not found.'], 404);

        $result = $this->packService->generatePack($data, $type);

        return response()->json([
            'success'  => true,
            'filename' => $result['filename'],
            'message'  => ucfirst($type) . ' pack generated successfully.',
        ]);
    }

    /**
     * Download a generated pack.
     */
    public function downloadPack(int $id, string $type)
    {
        $jobCard = DB::table('cims_job_cards')->where('id', $id)->first();
        if (!$jobCard) abort(404);

        $column = $type === 'external' ? 'external_pack_path' : 'internal_pack_path';
        $packPath = $jobCard->$column;

        if (!$packPath) abort(404, 'Pack not yet generated.');

        $fullPath = base_path('../storage/' . $packPath);
        if (!file_exists($fullPath)) abort(404, 'Pack file not found.');

        return response()->download($fullPath);
    }

    /**
     * Email a pack using CIMS_Email SMTP settings.
     */
    public function emailPack(Request $request, int $id)
    {
        $request->validate([
            'email_to' => 'required|email',
            'type'     => 'required|in:internal,external',
        ]);

        $jobCard = DB::table('cims_job_cards')->where('id', $id)->first();
        if (!$jobCard) return response()->json(['success' => false, 'message' => 'Job card not found.'], 404);

        $column = $request->input('type') === 'external' ? 'external_pack_path' : 'internal_pack_path';
        $packPath = $jobCard->$column;

        if (!$packPath) {
            // Generate on the fly
            $data = $this->service->getJobCard($id);
            $result = $this->packService->generatePack($data, $request->input('type'));
            $pdfContent = $result['pdf'];
            $filename = $result['filename'];
        } else {
            $fullPath = base_path('../storage/' . $packPath);
            $pdfContent = file_get_contents($fullPath);
            $filename = basename($packPath);
        }

        $this->loadSmtpSettings();

        $emailTo = $request->input('email_to');
        $subject = $request->input('subject', 'Job Card Pack - ' . $jobCard->job_code);
        $fromEmail = Config::get('mail.from.address');
        $fromName = Config::get('mail.from.name', 'CIMS');

        // Build email body with signature
        $signature = $this->getUserSignature();
        $signatureHtml = $this->buildSignatureHtml($signature);
        $disclaimerHtml = $this->buildDisclaimerHtml();

        $bodyHtml = '<!DOCTYPE html><html><head><meta charset="utf-8"></head>'
            . '<body style="margin:0;padding:0;width:100%;font-family:Arial,sans-serif;">'
            . '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="width:100%;"><tr><td style="padding:15px;">'
            . '<p style="font-size:14px;color:#333;">Please find attached the <strong>Job Card Pack</strong> for job <strong>'
            . e($jobCard->job_code) . '</strong>.</p>'
            . '<p style="font-size:14px;color:#333;">Kind regards</p>'
            . $signatureHtml
            . $disclaimerHtml
            . '</td></tr></table></body></html>';

        Mail::html($bodyHtml, function ($message) use ($emailTo, $subject, $pdfContent, $filename, $fromEmail, $fromName) {
            $message->from($fromEmail, $fromName);
            $message->to($emailTo);
            $message->subject($subject);
            $message->attachData($pdfContent, $filename, ['mime' => 'application/pdf']);
        });

        return response()->json(['success' => true, 'message' => 'Pack emailed to ' . $emailTo]);
    }

    /**
     * AJAX: Get directors/shareholders for BO workflow.
     */
    public function getDirectors(int $id)
    {
        $jobCard = DB::table('cims_job_cards')->where('id', $id)->first();
        if (!$jobCard) return response()->json(['success' => false, 'message' => 'Job card not found.'], 404);

        $data = $this->service->getDirectorsForClient($jobCard->client_id);
        $directorDocs = $this->service->getDirectorDocuments($id);

        return response()->json([
            'success' => true,
            'client' => $data['client'],
            'totalShares' => $data['totalShares'],
            'shareType' => $data['shareType'],
            'directors' => $data['directors'],
            'directorDocs' => $directorDocs,
        ]);
    }

    /**
     * AJAX: Fetch ID document from person record.
     */
    public function fetchIdDocument(Request $request, int $id)
    {
        $request->validate(['director_id' => 'required|integer']);
        $result = $this->service->fetchIdDocument($request->input('director_id'), $id);
        return response()->json($result);
    }

    /**
     * Upload ID document for a director in BO workflow.
     */
    public function uploadIdDocument(Request $request, int $id)
    {
        $request->validate([
            'document' => 'required|file|max:20480',
            'director_id' => 'required|integer',
            'document_category' => 'nullable|string',
        ]);

        $file = $request->file('document');
        $storagePath = base_path('../storage/' . config('job_cards.documents_path', 'job_cards/documents'));
        if (!is_dir($storagePath)) mkdir($storagePath, 0755, true);

        $storedName = time() . '_' . $file->getClientOriginalName();
        $file->move($storagePath, $storedName);

        DB::table('cims_job_card_attachments')->insert([
            'job_card_id' => $id,
            'file_name' => $storedName,
            'file_original_name' => $file->getClientOriginalName(),
            'file_path' => config('job_cards.documents_path', 'job_cards/documents') . '/' . $storedName,
            'file_mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'file_type' => 'id_document',
            'uploaded_by' => Auth::id(),
            'director_id' => $request->input('director_id'),
            'document_category' => $request->input('document_category', 'id_front'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'ID document uploaded.']);
    }

    /**
     * AJAX: Fetch POA from person record.
     */
    public function fetchPOA(Request $request, int $id)
    {
        $request->validate(['director_id' => 'required|integer']);
        $result = $this->boService->fetchPOA($id, $request->input('director_id'));
        return response()->json($result);
    }

    /**
     * Upload POA document for a director.
     */
    public function uploadPOA(Request $request, int $id)
    {
        $request->validate([
            'document' => 'required|file|max:20480',
            'director_id' => 'required|integer',
        ]);
        $result = $this->boService->uploadPOA($id, $request->input('director_id'), $request->file('document'));
        return response()->json($result);
    }

    /**
     * Generate CRA01 SARS form for a director.
     */
    public function generateCRA01(Request $request, int $id)
    {
        $request->validate(['director_id' => 'required|integer']);
        $result = $this->boService->generateCRA01($id, $request->input('director_id'));
        return response()->json($result);
    }

    /**
     * Generate a specific BO document.
     */
    public function generateBODocument(Request $request, int $id)
    {
        $request->validate(['doc_type' => 'required|in:shareholders,beneficial_owners,diagram,resolution,all']);

        $jobCard = DB::table('cims_job_cards')->where('id', $id)->first();
        if (!$jobCard) return response()->json(['success' => false, 'message' => 'Job card not found.'], 404);

        $docType = $request->input('doc_type');

        try {
            if ($docType === 'all') {
                $results = $this->boService->generateAllBODocuments($id, $jobCard->client_id);
                return response()->json(['success' => true, 'message' => 'All 4 BO documents generated.', 'results' => $results]);
            }

            $result = match ($docType) {
                'shareholders' => $this->boService->generateShareholderRegister($id, $jobCard->client_id),
                'beneficial_owners' => $this->boService->generateBeneficialOwnerRegister($id, $jobCard->client_id),
                'diagram' => $this->boService->generateBODiagram($id, $jobCard->client_id),
                'resolution' => $this->boService->generateOrdinaryResolution($id, $jobCard->client_id),
            };

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get full BO review data (read-only, auto-populated).
     */
    public function getBOReview(int $id)
    {
        $jobCard = DB::table('cims_job_cards')->where('id', $id)->first();
        if (!$jobCard) return response()->json(['success' => false, 'message' => 'Job card not found.'], 404);

        $data = $this->boService->getBOData($jobCard->client_id);
        $directorDocs = $this->service->getDirectorDocuments($id);

        return response()->json([
            'success'      => true,
            'client'       => $data['client'] ?? null,
            'totalShares'  => $data['totalShares'] ?? 0,
            'shareType'    => $data['shareType'] ?? 'Ordinary Shares',
            'directors'    => $data['directors'] ?? [],
            'certificates' => $data['certificates'] ?? [],
            'agentCode'    => $data['agentCode'] ?? 'CYMDIM',
            'companySettings' => $data['companySettings'] ?? [],
            'directorDocs' => $directorDocs,
        ]);
    }

    /**
     * Download a BO attachment (PDF, POA, etc.).
     */
    public function downloadBOAttachment(int $id, int $attachmentId)
    {
        $attachment = DB::table('cims_job_card_attachments')
            ->where('id', $attachmentId)
            ->where('job_card_id', $id)
            ->first();

        if (!$attachment) abort(404, 'Attachment not found.');

        $fullPath = base_path('../storage/' . $attachment->file_path);
        if (!file_exists($fullPath)) abort(404, 'File not found.');

        return response()->download($fullPath, $attachment->file_original_name);
    }

    /**
     * Email BO documents to client — InfoDocs-style with clickable document links.
     */
    public function emailBODocuments(Request $request, int $id)
    {
        $request->validate([
            'email_to' => 'required|email',
        ]);

        $jobCard = DB::table('cims_job_cards')->where('id', $id)->first();
        if (!$jobCard) return response()->json(['success' => false, 'message' => 'Job card not found.'], 404);

        $client = DB::table('client_master')->where('client_id', $jobCard->client_id)->first();
        if (!$client) return response()->json(['success' => false, 'message' => 'Client not found.'], 404);

        // Check if the 4 core BO documents exist — auto-generate if missing
        $boDocCount = DB::table('cims_job_card_attachments')
            ->where('job_card_id', $id)
            ->where('file_type', 'bo_document')
            ->count();

        if ($boDocCount < 4) {
            try {
                $this->boService->generateAllBODocuments($id, $jobCard->client_id);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Failed to auto-generate BO documents: ' . $e->getMessage()], 500);
            }
        }

        // Get all BO attachments for this job card
        $attachments = DB::table('cims_job_card_attachments')
            ->where('job_card_id', $id)
            ->whereIn('file_type', ['bo_document', 'id_document', 'poa_document'])
            ->orderByRaw("FIELD(file_type, 'id_document', 'poa_document', 'bo_document')")
            ->orderBy('created_at', 'desc')
            ->get();

        if ($attachments->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No documents could be generated. Please check the client data.']);
        }

        // Deduplicate — keep only the latest per document_category + director_id combo
        $seen = [];
        $uniqueAttachments = [];
        foreach ($attachments as $att) {
            $key = ($att->document_category ?? 'other') . '_' . ($att->director_id ?? 0);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $uniqueAttachments[] = $att;
            }
        }

        // Build download URLs
        $baseUrl = url('/job-cards/' . $id . '/bo-attachment');
        $companyName = $client->company_name ?? 'Unknown Company';
        $regNumber = $client->company_reg_number ?? '';

        // Group documents for nice display
        $docLinks = '';
        foreach ($uniqueAttachments as $att) {
            $downloadUrl = $baseUrl . '/' . $att->id;
            $label = $att->file_original_name ?: $att->file_name;

            // Prettify label based on document category
            $category = $att->document_category ?? '';
            if ($category === 'register_of_shareholders') {
                $label = strtoupper($companyName) . ' - Register of Shareholders';
            } elseif ($category === 'register_of_beneficial_owners') {
                $label = strtoupper($companyName) . ' - Register of Beneficial Owners';
            } elseif ($category === 'beneficial_ownership_diagram') {
                $label = strtoupper($companyName) . ' - Beneficial Ownership Diagram';
            } elseif ($category === 'ordinary_resolution') {
                $label = 'Ordinary Resolution - Mandate to Lodge Beneficial Ownership';
            } elseif ($category === 'cra01') {
                // Get director name if available
                if ($att->director_id) {
                    $dir = DB::table('client_master_directors')->where('id', $att->director_id)->first();
                    if ($dir) $label = 'CRA01 - ' . $dir->firstname . ' ' . $dir->surname;
                }
            } elseif ($att->file_type === 'id_document') {
                if ($att->director_id) {
                    $dir = DB::table('client_master_directors')->where('id', $att->director_id)->first();
                    if ($dir) $label = 'Verified/Certified ID - ' . strtoupper($dir->firstname . ' ' . $dir->surname);
                }
            } elseif ($att->file_type === 'poa_document' && $category !== 'cra01') {
                if ($att->director_id) {
                    $dir = DB::table('client_master_directors')->where('id', $att->director_id)->first();
                    if ($dir) $label = 'Proof of Address - ' . strtoupper($dir->firstname . ' ' . $dir->surname);
                }
            }

            $docLinks .= '<li style="margin-bottom:8px;">'
                . '<a href="' . e($downloadUrl) . '" style="color:#1a73e8;font-size:14px;text-decoration:none;font-weight:500;">'
                . e($label) . '</a>'
                . '</li>';
        }

        // Build the InfoDocs-style email body
        $this->loadSmtpSettings();

        $emailTo = $request->input('email_to');
        $subject = 'Beneficial Ownership Submission for ' . strtoupper($companyName);
        $fromEmail = Config::get('mail.from.address');
        $fromName = Config::get('mail.from.name', 'CIMS');

        $signature = $this->getUserSignature();
        $signatureHtml = $this->buildSignatureHtml($signature);
        $disclaimerHtml = $this->buildDisclaimerHtml();

        $regDisplay = $regNumber ? ' (' . e($regNumber) . ')' : '';

        $bodyHtml = '<!DOCTYPE html><html><head><meta charset="utf-8"></head>'
            . '<body style="margin:0;padding:0;width:100%;font-family:Arial,Helvetica,sans-serif;background:#f5f5f5;">'
            . '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="width:100%;max-width:650px;margin:0 auto;background:#ffffff;">'
            . '<tr><td style="padding:30px 35px;">'

            // Header
            . '<p style="font-size:15px;color:#333;line-height:1.6;margin:0 0 20px 0;">'
            . 'This is your automated document request.'
            . '</p>'

            // Company reference
            . '<p style="font-size:15px;color:#333;line-height:1.6;margin:0 0 15px 0;">'
            . 'Below are the supporting documents required for filing the beneficial ownership for '
            . '<a href="#" style="color:#1a73e8;font-weight:600;text-decoration:underline;">'
            . strtoupper(e($companyName)) . $regDisplay
            . '</a>'
            . '</p>'

            // Document links
            . '<ul style="padding-left:25px;margin:15px 0 25px 0;list-style-type:disc;">'
            . $docLinks
            . '</ul>'

            // Closing
            . '<p style="font-size:14px;color:#333;margin:25px 0 5px 0;">Sincerely,</p>'

            // Signature
            . $signatureHtml

            // Disclaimer
            . $disclaimerHtml

            . '</td></tr></table></body></html>';

        // Also attach all PDFs to the email for offline access
        $pdfAttachments = [];
        foreach ($uniqueAttachments as $att) {
            $fullPath = base_path('../storage/' . $att->file_path);
            if (file_exists($fullPath) && str_ends_with(strtolower($att->file_name), '.pdf')) {
                $pdfAttachments[] = [
                    'content'  => file_get_contents($fullPath),
                    'filename' => $att->file_original_name ?: $att->file_name,
                    'mime'     => 'application/pdf',
                ];
            }
        }

        Mail::html($bodyHtml, function ($message) use ($emailTo, $subject, $fromEmail, $fromName, $pdfAttachments) {
            $message->from($fromEmail, $fromName);
            $message->to($emailTo);
            $message->subject($subject);
            foreach ($pdfAttachments as $pdf) {
                $message->attachData($pdf['content'], $pdf['filename'], ['mime' => $pdf['mime']]);
            }
        });

        return response()->json(['success' => true, 'message' => 'BO documents emailed to ' . $emailTo]);
    }

    /**
     * AJAX: Get client info for a selected client + job type.
     */
    public function getClientInfo(int $clientId, Request $request)
    {
        $jobTypeId = $request->input('job_type_id');
        $data = $this->service->getClientInfo($clientId, $jobTypeId);
        return response()->json($data);
    }

    /**
     * AJAX: Search clients.
     */
    public function searchClients(Request $request)
    {
        $query = $request->input('q', '');
        $clients = $this->service->searchClients($query);
        return response()->json($clients);
    }

    /**
     * AJAX: Get job type configuration (steps, fields, docs).
     */
    public function getJobTypeConfig(int $typeId)
    {
        $config = $this->service->getJobTypeConfig($typeId);
        return response()->json($config);
    }

    // ─── Email helpers (same pattern as AgedAnalysis) ───

    protected function loadSmtpSettings(): void
    {
        $settings = DB::table('cims_email_settings')
            ->pluck('setting_value', 'setting_key')
            ->toArray();

        if (!empty($settings)) {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', $settings['smtp_host'] ?? '');
            Config::set('mail.mailers.smtp.port', $settings['smtp_port'] ?? 587);
            Config::set('mail.mailers.smtp.encryption', $settings['smtp_encryption'] ?? 'tls');
            Config::set('mail.mailers.smtp.username', $settings['smtp_username'] ?? '');
            Config::set('mail.mailers.smtp.password', $settings['smtp_password'] ?? '');
            Config::set('mail.from.address', $settings['from_email'] ?? '');
            Config::set('mail.from.name', $settings['from_name'] ?? 'CIMS');
            app('mail.manager')->purge('smtp');
        }
    }

    private function getUserSignature()
    {
        try {
            $sig = DB::table('cims_email_signatures')
                ->where('user_id', Auth::id())
                ->where('is_active', 1)
                ->first();
            if (!$sig) {
                $sig = DB::table('cims_email_signatures')
                    ->where('is_active', 1)
                    ->first();
            }
            return $sig;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function buildDisclaimerHtml()
    {
        try {
            $disclaimer = DB::table('cims_email_settings')
                ->where('setting_key', 'disclaimer_html')
                ->value('setting_value');
        } catch (\Exception $e) {
            $disclaimer = null;
        }
        if (empty($disclaimer)) return '';
        return '<div style="margin-top:15px;padding:10px 12px;border-top:1px solid #ddd;font-size:10px;color:#888;line-height:1.5;background:#fafafa;border-radius:4px;">'
            . nl2br(htmlspecialchars($disclaimer))
            . '</div>';
    }

    private function buildSignatureHtml($signature)
    {
        if (!$signature) return '';
        if (!empty($signature->signature_html)) return $signature->signature_html;

        $name = $signature->full_name ?? '';
        $title = $signature->designation ?? '';
        $phone = $signature->phone ?? '';
        $mobile = $signature->mobile ?? '';
        $direct = $signature->direct_number ?? '';
        $whatsapp = $signature->whatsapp ?? '';
        $company = $signature->company_name ?? '';
        $website = $signature->company_website ?? '';
        $slogan = $signature->slogan ?? '';

        if (empty($name)) return '';

        $bannerUrl = '';
        try {
            $bannerUrl = DB::table('cims_email_settings')
                ->where('setting_key', 'signature_banner_url')
                ->value('setting_value') ?? '';
        } catch (\Exception $e) {}

        $logoUrl = url('/assets/cims_core/atp_cims_logo.jpg');
        $stampUrl = url('/assets/cims_core/atpstamp.jpg');
        $websiteUrl = '';
        if ($website) {
            $websiteUrl = $website;
            if (!preg_match('/^https?:\/\//', $websiteUrl)) $websiteUrl = 'https://' . $websiteUrl;
        }

        $html = '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:Arial,sans-serif;font-size:13px;color:#333;border-collapse:collapse;width:100%;">';
        $html .= '<tr><td style="vertical-align:top;">';
        $html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:Arial,sans-serif;font-size:13px;color:#333;border-collapse:collapse;width:100%;">';

        $html .= '<tr><td style="padding-bottom:10px;border-bottom:3px solid #E91E8C;">';
        $html .= '<strong style="font-size:16px;color:#1a1a2e;letter-spacing:0.5px;">' . htmlspecialchars($name) . '</strong>';
        if ($title) $html .= '<br><span style="font-size:12px;color:#777;margin-top:2px;display:inline-block;">' . htmlspecialchars($title) . '</span>';
        $html .= '</td></tr>';

        $contactParts = [];
        if ($phone) $contactParts[] = '<span style="color:#2196F3;">Tel:</span> ' . htmlspecialchars($phone);
        if ($direct) $contactParts[] = '<span style="color:#9C27B0;">Direct:</span> ' . htmlspecialchars($direct);
        if ($mobile) $contactParts[] = '<span style="color:#FF6B35;">Mobile:</span> ' . htmlspecialchars($mobile);
        if ($whatsapp) {
            $waNum = preg_replace('/[^0-9]/', '', $whatsapp);
            $contactParts[] = '<span style="color:#25D366;">WhatsApp:</span> <a href="https://wa.me/' . $waNum . '" style="color:#444;text-decoration:none;" target="_blank">' . htmlspecialchars($whatsapp) . '</a>';
        }
        if (!empty($contactParts)) {
            $html .= '<tr><td style="padding:12px 0 10px 0;">';
            $html .= '<span style="font-size:12px;color:#444;">' . implode(' &nbsp;<span style="color:#ccc;">|</span>&nbsp; ', $contactParts) . '</span>';
            $html .= '</td></tr>';
        }

        $html .= '<tr><td style="padding:0;"><div style="border-top:1px solid #eee;"></div></td></tr>';

        if ($company) {
            $html .= '<tr><td style="padding-top:10px;">';
            $html .= '<strong style="font-size:14px;color:#1a1a2e;">' . htmlspecialchars($company) . '</strong>';
            if ($website) $html .= ' <span style="color:#ccc;">|</span> <a href="' . $websiteUrl . '" style="font-size:13px;color:#0066CC;text-decoration:none;">' . htmlspecialchars($website) . '</a>';
            $html .= '</td></tr>';
        }

        if ($slogan) {
            $html .= '<tr><td style="padding-top:4px;">';
            $html .= '<em style="font-size:12px;color:#E91E8C;font-style:italic;letter-spacing:0.3px;">' . htmlspecialchars($slogan) . '</em>';
            $html .= '</td></tr>';
        }

        if ($bannerUrl) {
            $html .= '<tr><td style="padding-top:14px;">';
            $html .= '<img src="' . htmlspecialchars($bannerUrl) . '" alt="Signature Banner" style="max-width:100%;height:auto;border-radius:4px;">';
            $html .= '</td></tr>';
        }

        $html .= '</table>';
        $html .= '</td>';
        $html .= '<td style="vertical-align:middle;text-align:right;padding-left:20px;width:180px;">';
        $html .= '<img src="' . $stampUrl . '" alt="ATP Stamp" style="width:170px;height:auto;opacity:0.85;">';
        $html .= '</td></tr>';

        $html .= '<tr><td colspan="2" style="padding-top:14px;"><div style="border-top:1px solid #e0e0e0;"></div></td></tr>';

        $logoLinkStart = $websiteUrl ? '<a href="' . $websiteUrl . '" target="_blank" style="text-decoration:none;">' : '';
        $logoLinkEnd = $websiteUrl ? '</a>' : '';
        $html .= '<tr><td colspan="2" style="padding-top:10px;text-align:left;">';
        $html .= $logoLinkStart;
        $html .= '<img src="' . $logoUrl . '" alt="CIMS" style="max-width:280px;height:auto;">';
        $html .= $logoLinkEnd;
        $html .= '</td></tr>';

        $html .= '</table>';
        return $html;
    }
}
