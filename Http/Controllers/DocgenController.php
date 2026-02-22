<?php

namespace Modules\DG2026\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\DG2026\Models\DocgenTemplate;
use Modules\DG2026\Models\DocgenTemplatePage;
use Modules\DG2026\Models\DocgenFieldMapping;
use Modules\DG2026\Models\DocgenDocument;
use Modules\DG2026\Models\DocgenAuditLog;
use Modules\DG2026\Models\DocgenSetting;
use Modules\DG2026\Services\PdfGeneratorService;

class DocgenController extends Controller
{
    // ─── DOCUMENTS ─────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = DocgenDocument::with('template')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('document_name', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('client_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('template_id')) {
            $query->where('template_id', $request->input('template_id'));
        }

        $documents = $query->paginate(20);
        $templates = DocgenTemplate::where('is_active', true)->orderBy('name')->get();

        return view('dg2026::documents.index', compact('documents', 'templates'));
    }

    public function show($id)
    {
        $document = DocgenDocument::with(['template', 'auditLogs'])->findOrFail($id);

        $this->logAction($document, 'viewed', 'Document viewed');

        return view('dg2026::documents.show', compact('document'));
    }

    public function viewer($id)
    {
        $document = DocgenDocument::findOrFail($id);

        if (!$document->file_path || !file_exists(storage_path('app/' . $document->file_path))) {
            return back()->with('error', 'Document file not found.');
        }

        return response()->file(storage_path('app/' . $document->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . ($document->file_name ?: 'document.pdf') . '"',
        ]);
    }

    public function download($id)
    {
        $document = DocgenDocument::findOrFail($id);

        if (!$document->file_path || !file_exists(storage_path('app/' . $document->file_path))) {
            return back()->with('error', 'Document file not found.');
        }

        $this->logAction($document, 'downloaded', 'Document downloaded');

        return response()->download(
            storage_path('app/' . $document->file_path),
            $document->file_name ?: $document->document_name . '.pdf'
        );
    }

    // ─── GENERATE DOCUMENT ────────────────────────────────────

    public function create()
    {
        $templates = DocgenTemplate::where('is_active', true)->orderBy('name')->get();

        return view('dg2026::documents.create', compact('templates'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:docgen_templates,id',
            'client_id' => 'required',
            'document_name' => 'required|string|max:255',
            'document_date' => 'nullable|date',
        ]);

        $template = DocgenTemplate::with('activePages.activeFieldMappings')->findOrFail($request->template_id);

        // Get client data
        $client = DB::table('client_master')->where('client_id', $request->client_id)->first();

        if (!$client) {
            return back()->with('error', 'Client not found.')->withInput();
        }

        $clientData = (array) $client;

        // Add form input fields
        $clientData['requested_by'] = $request->input('requested_by', '');
        $clientData['prepared_by'] = $request->input('prepared_by', '');
        $clientData['approved_by'] = $request->input('approved_by', '');
        $clientData['signed_by'] = $request->input('signed_by', '');
        $clientData['document_date'] = $request->input('document_date', date('Y-m-d'));

        // Generate unique document number
        $prefix = strtoupper($template->code);
        $docNumber = $prefix . '-' . $client->client_code . '-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        // Output path
        $fileName = Str::slug($request->document_name) . '_' . date('Ymd_His') . '.pdf';
        $storagePath = 'docgen/documents/' . date('Y/m') . '/' . $fileName;
        $fullPath = storage_path('app/' . $storagePath);

        // Generate PDF
        try {
            $service = new PdfGeneratorService();
            $service->generate($template, $clientData, $fullPath);
        } catch (\Exception $e) {
            return back()->with('error', 'PDF generation failed: ' . $e->getMessage())->withInput();
        }

        // Create document record
        $document = DocgenDocument::create([
            'template_id' => $template->id,
            'client_id' => $client->client_id,
            'client_code' => $client->client_code,
            'client_name' => $client->company_name,
            'document_name' => $request->document_name,
            'document_number' => $docNumber,
            'file_path' => $storagePath,
            'file_name' => $fileName,
            'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0,
            'requested_by' => $request->input('requested_by'),
            'prepared_by' => $request->input('prepared_by'),
            'approved_by' => $request->input('approved_by'),
            'signed_by' => $request->input('signed_by'),
            'document_date' => $request->input('document_date'),
            'notes' => $request->input('notes'),
            'status' => 'active',
            'generated_by' => auth()->id(),
        ]);

        $this->logAction($document, 'generated', 'Document generated from template: ' . $template->name);

        return redirect()->route('docgen.show', $document->id)
            ->with('success', 'Document generated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $document = DocgenDocument::findOrFail($id);
        $oldStatus = $document->status;
        $newStatus = $request->input('status');

        if (!in_array($newStatus, ['active', 'inactive', 'deleted'])) {
            return back()->with('error', 'Invalid status.');
        }

        $document->update(['status' => $newStatus]);
        $this->logAction($document, 'status_changed', "Status changed from {$oldStatus} to {$newStatus}");

        return back()->with('success', 'Document status updated.');
    }

    public function destroy($id): JsonResponse
    {
        $document = DocgenDocument::findOrFail($id);

        $this->logAction($document, 'deleted', 'Document soft-deleted');
        $document->delete();

        return response()->json(['success' => true, 'message' => 'Document deleted.']);
    }

    // ─── EMAIL ────────────────────────────────────────────────

    public function email(Request $request, $id)
    {
        $document = DocgenDocument::findOrFail($id);

        $request->validate([
            'email_to' => 'required|email',
            'email_subject' => 'required|string|max:255',
            'email_body' => 'nullable|string',
        ]);

        $filePath = storage_path('app/' . $document->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'Document file not found.');
        }

        // Use docgen SMTP settings
        $smtpHost = DocgenSetting::getVal('smtp_host', config('mail.mailers.smtp.host'));
        $smtpPort = DocgenSetting::getVal('smtp_port', config('mail.mailers.smtp.port'));
        $smtpUser = DocgenSetting::getVal('smtp_username', config('mail.mailers.smtp.username'));
        $smtpPass = DocgenSetting::getVal('smtp_password', config('mail.mailers.smtp.password'));
        $smtpEncryption = DocgenSetting::getVal('smtp_encryption', config('mail.mailers.smtp.encryption'));
        $smtpFrom = DocgenSetting::getVal('smtp_from_address', config('mail.from.address'));
        $smtpFromName = DocgenSetting::getVal('smtp_from_name', config('mail.from.name'));

        // Temporarily override mail config
        config([
            'mail.mailers.smtp.host' => $smtpHost,
            'mail.mailers.smtp.port' => $smtpPort,
            'mail.mailers.smtp.username' => $smtpUser,
            'mail.mailers.smtp.password' => $smtpPass,
            'mail.mailers.smtp.encryption' => $smtpEncryption,
            'mail.from.address' => $smtpFrom,
            'mail.from.name' => $smtpFromName,
        ]);

        try {
            $emailTo = $request->input('email_to');
            $emailSubject = $request->input('email_subject');
            $emailBody = $request->input('email_body', 'Please find the attached document.');

            Mail::raw($emailBody, function ($message) use ($emailTo, $emailSubject, $filePath, $document, $smtpFrom, $smtpFromName) {
                $message->to($emailTo)
                    ->subject($emailSubject)
                    ->from($smtpFrom, $smtpFromName)
                    ->attach($filePath, [
                        'as' => $document->file_name ?: $document->document_name . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
            });

            $document->update([
                'emailed' => true,
                'emailed_to' => $emailTo,
                'emailed_at' => now(),
            ]);

            $this->logAction($document, 'emailed', "Document emailed to {$emailTo}");

            return back()->with('success', 'Document emailed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Email failed: ' . $e->getMessage());
        }
    }

    // ─── TEMPLATES ────────────────────────────────────────────

    public function templates(Request $request)
    {
        $query = DocgenTemplate::withCount('pages', 'documents')->orderBy('sort_order');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $templates = $query->paginate(20);

        return view('dg2026::templates.index', compact('templates'));
    }

    public function templateCreate()
    {
        return view('dg2026::templates.create');
    }

    public function templateStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:docgen_templates,code',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
        ]);

        $template = DocgenTemplate::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'category' => $request->category,
            'is_active' => true,
            'sort_order' => DocgenTemplate::max('sort_order') + 1,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('docgen.templates.edit', $template->id)
            ->with('success', 'Template created. Now add pages and configure field mappings.');
    }

    public function templateEdit($id)
    {
        $template = DocgenTemplate::with(['pages' => function ($q) {
            $q->orderBy('sort_order');
        }, 'pages.fieldMappings' => function ($q) {
            $q->orderBy('sort_order');
        }])->findOrFail($id);

        $clientFields = $this->getClientFields();

        return view('dg2026::templates.edit', compact('template', 'clientFields'));
    }

    public function templateUpdate(Request $request, $id)
    {
        $template = DocgenTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:docgen_templates,code,' . $id,
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $template->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'category' => $request->category,
            'is_active' => $request->boolean('is_active'),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Template updated.');
    }

    public function templateDestroy($id): JsonResponse
    {
        $template = DocgenTemplate::findOrFail($id);
        $template->delete();

        return response()->json(['success' => true, 'message' => 'Template deleted.']);
    }

    // ─── TEMPLATE PAGES ───────────────────────────────────────

    public function pageStore(Request $request, $id)
    {
        $template = DocgenTemplate::findOrFail($id);

        $request->validate([
            'page_label' => 'nullable|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'orientation' => 'required|in:portrait,landscape',
        ]);

        $nextPage = $template->pages()->max('page_number') + 1;
        $nextSort = $template->pages()->max('sort_order') + 1;

        // Store the PDF file
        $path = $request->file('pdf_file')->store('docgen/templates/' . $template->id . '/pages');

        $page = DocgenTemplatePage::create([
            'template_id' => $template->id,
            'page_number' => $nextPage,
            'page_label' => $request->page_label ?: 'Page ' . $nextPage,
            'pdf_path' => $path,
            'orientation' => $request->orientation,
            'is_active' => true,
            'sort_order' => $nextSort,
        ]);

        return back()->with('success', 'Page added successfully.');
    }

    public function pageUpdate(Request $request, $id): JsonResponse
    {
        $page = DocgenTemplatePage::findOrFail($id);

        $page->update($request->only(['page_label', 'orientation', 'is_active', 'sort_order']));

        return response()->json(['success' => true, 'message' => 'Page updated.']);
    }

    public function pageDestroy($id): JsonResponse
    {
        $page = DocgenTemplatePage::findOrFail($id);

        // Delete the stored PDF
        $filePath = storage_path('app/' . $page->pdf_path);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        $page->fieldMappings()->delete();
        $page->delete();

        return response()->json(['success' => true, 'message' => 'Page deleted.']);
    }

    public function pageReorder(Request $request): JsonResponse
    {
        $request->validate([
            'pages' => 'required|array',
            'pages.*.id' => 'required|integer',
            'pages.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->pages as $item) {
            DocgenTemplatePage::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
                'page_number' => $item['sort_order'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Pages reordered.']);
    }

    // ─── FIELD MAPPINGS ───────────────────────────────────────

    public function fields($id)
    {
        $page = DocgenTemplatePage::with(['template', 'fieldMappings' => function ($q) {
            $q->orderBy('sort_order');
        }])->findOrFail($id);

        $clientFields = $this->getClientFields();

        return view('dg2026::templates.fields', compact('page', 'clientFields'));
    }

    public function fieldStore(Request $request, $id): JsonResponse
    {
        $page = DocgenTemplatePage::findOrFail($id);

        $request->validate([
            'field_name' => 'required|string|max:255',
            'field_label' => 'required|string|max:255',
            'pos_x' => 'required|numeric|min:0',
            'pos_y' => 'required|numeric|min:0',
        ]);

        $nextSort = $page->fieldMappings()->max('sort_order') + 1;

        $field = DocgenFieldMapping::create([
            'template_page_id' => $page->id,
            'field_name' => $request->field_name,
            'field_label' => $request->field_label,
            'field_source' => $request->input('field_source', 'client_master'),
            'pos_x' => $request->pos_x,
            'pos_y' => $request->pos_y,
            'width' => $request->input('width'),
            'height' => $request->input('height'),
            'font_family' => $request->input('font_family'),
            'font_size' => $request->input('font_size'),
            'font_style' => $request->input('font_style'),
            'font_color' => $request->input('font_color'),
            'text_align' => $request->input('text_align', 'left'),
            'field_type' => $request->input('field_type', 'text'),
            'date_format' => $request->input('date_format'),
            'default_value' => $request->input('default_value'),
            'is_active' => true,
            'sort_order' => $nextSort,
        ]);

        return response()->json(['success' => true, 'field' => $field, 'message' => 'Field mapping added.']);
    }

    public function fieldUpdate(Request $request, $id): JsonResponse
    {
        $field = DocgenFieldMapping::findOrFail($id);

        $field->update($request->only([
            'field_name', 'field_label', 'field_source',
            'pos_x', 'pos_y', 'width', 'height',
            'font_family', 'font_size', 'font_style', 'font_color', 'text_align',
            'field_type', 'date_format', 'default_value', 'is_active', 'sort_order',
        ]));

        return response()->json(['success' => true, 'field' => $field, 'message' => 'Field mapping updated.']);
    }

    public function fieldDestroy($id): JsonResponse
    {
        DocgenFieldMapping::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Field mapping deleted.']);
    }

    // ─── SETTINGS ─────────────────────────────────────────────

    public function settings()
    {
        $settings = [
            'default_font_family' => DocgenSetting::getVal('default_font_family', 'Helvetica'),
            'default_font_size' => DocgenSetting::getVal('default_font_size', '10'),
            'default_font_color' => DocgenSetting::getVal('default_font_color', '#000000'),
            'default_text_align' => DocgenSetting::getVal('default_text_align', 'left'),
            'company_name' => DocgenSetting::getVal('company_name', ''),
            'company_logo_path' => DocgenSetting::getVal('company_logo_path', ''),
            'document_storage_path' => DocgenSetting::getVal('document_storage_path', 'docgen/documents'),
        ];

        return view('dg2026::settings.general', compact('settings'));
    }

    public function settingsSave(Request $request)
    {
        $keys = [
            'default_font_family', 'default_font_size', 'default_font_color',
            'default_text_align', 'company_name', 'document_storage_path',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                DocgenSetting::setVal($key, $request->input($key));
            }
        }

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('docgen/settings');
            DocgenSetting::setVal('company_logo_path', $path);
        }

        return back()->with('success', 'Settings saved successfully.');
    }

    // ─── SMTP SETTINGS ───────────────────────────────────────

    public function smtp()
    {
        $settings = [
            'smtp_host' => DocgenSetting::getVal('smtp_host', ''),
            'smtp_port' => DocgenSetting::getVal('smtp_port', '587'),
            'smtp_username' => DocgenSetting::getVal('smtp_username', ''),
            'smtp_password' => DocgenSetting::getVal('smtp_password', ''),
            'smtp_encryption' => DocgenSetting::getVal('smtp_encryption', 'tls'),
            'smtp_from_address' => DocgenSetting::getVal('smtp_from_address', ''),
            'smtp_from_name' => DocgenSetting::getVal('smtp_from_name', ''),
        ];

        return view('dg2026::settings.smtp', compact('settings'));
    }

    public function smtpSave(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_from_address' => 'required|email',
        ]);

        $keys = [
            'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password',
            'smtp_encryption', 'smtp_from_address', 'smtp_from_name',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                DocgenSetting::setVal($key, $request->input($key));
            }
        }

        return back()->with('success', 'SMTP settings saved successfully.');
    }

    public function smtpTest(Request $request): JsonResponse
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $smtpHost = DocgenSetting::getVal('smtp_host');
        $smtpPort = DocgenSetting::getVal('smtp_port', 587);
        $smtpUser = DocgenSetting::getVal('smtp_username');
        $smtpPass = DocgenSetting::getVal('smtp_password');
        $smtpEncryption = DocgenSetting::getVal('smtp_encryption', 'tls');
        $smtpFrom = DocgenSetting::getVal('smtp_from_address');
        $smtpFromName = DocgenSetting::getVal('smtp_from_name', 'DocGen');

        if (!$smtpHost || !$smtpFrom) {
            return response()->json(['success' => false, 'message' => 'Please save SMTP settings first.']);
        }

        config([
            'mail.mailers.smtp.host' => $smtpHost,
            'mail.mailers.smtp.port' => $smtpPort,
            'mail.mailers.smtp.username' => $smtpUser,
            'mail.mailers.smtp.password' => $smtpPass,
            'mail.mailers.smtp.encryption' => $smtpEncryption,
            'mail.from.address' => $smtpFrom,
            'mail.from.name' => $smtpFromName,
        ]);

        try {
            Mail::raw('This is a test email from the Document Generator module.', function ($message) use ($request, $smtpFrom, $smtpFromName) {
                $message->to($request->test_email)
                    ->subject('DocGen SMTP Test')
                    ->from($smtpFrom, $smtpFromName);
            });

            return response()->json(['success' => true, 'message' => 'Test email sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'SMTP test failed: ' . $e->getMessage()]);
        }
    }

    // ─── API ENDPOINTS ────────────────────────────────────────

    public function apiClients(Request $request): JsonResponse
    {
        $search = $request->input('q', '');

        $query = DB::table('client_master')
            ->select('client_id', 'company_name', 'client_code', 'trading_name')
            ->whereNull('deleted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('client_code', 'like', "%{$search}%")
                  ->orWhere('trading_name', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('company_name')->limit(50)->get();

        return response()->json($clients);
    }

    public function apiClientDetail($id): JsonResponse
    {
        $client = DB::table('client_master')->where('client_id', $id)->first();

        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        return response()->json($client);
    }

    // ─── HELPERS ──────────────────────────────────────────────

    protected function logAction(DocgenDocument $document, string $action, string $details = '')
    {
        DocgenAuditLog::create([
            'document_id' => $document->id,
            'action' => $action,
            'action_by' => auth()->user()->name ?? 'System',
            'action_by_id' => auth()->id(),
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }

    protected function getClientFields(): array
    {
        return [
            'Company Details' => [
                'company_name' => 'Company Name',
                'trading_name' => 'Trading Name',
                'client_code' => 'Client Code',
                'company_reg_number' => 'Company Reg Number',
                'company_type' => 'Company Type',
                'company_reg_date' => 'Registration Date',
                'financial_year_end' => 'Financial Year End',
                'number_of_directors' => 'Number of Directors',
                'number_of_shares' => 'Number of Shares',
                'share_type_name' => 'Share Type',
                'bizportal_number' => 'BizPortal Number',
            ],
            'Tax & Compliance' => [
                'tax_number' => 'Tax Number',
                'tax_reg_date' => 'Tax Registration Date',
                'vat_number' => 'VAT Number',
                'vat_reg_date' => 'VAT Registration Date',
                'vat_return_cycle' => 'VAT Return Cycle',
                'vat_cycle_name' => 'VAT Cycle Name',
                'vat_effect_from' => 'VAT Effective From',
                'paye_number' => 'PAYE Number',
                'sdl_number' => 'SDL Number',
                'uif_number' => 'UIF Number',
                'dept_labour_number' => 'Dept Labour Number',
                'wca_coida_number' => 'WCA/COIDA Number',
                'payroll_liability_date' => 'Payroll Liability Date',
                'cipc_annual_returns' => 'CIPC Annual Returns',
            ],
            'Contact Details' => [
                'phone_business' => 'Business Phone',
                'phone_mobile' => 'Mobile Phone',
                'direct' => 'Direct Line',
                'phone_whatsapp' => 'WhatsApp Number',
                'email' => 'Email Address',
                'email_admin' => 'Admin Email',
                'website' => 'Website',
            ],
            'SARS Representative' => [
                'sars_rep_first_name' => 'SARS Rep First Name',
                'sars_rep_middle_name' => 'SARS Rep Middle Name',
                'sars_rep_surname' => 'SARS Rep Surname',
                'sars_rep_initial' => 'SARS Rep Initial',
                'sars_rep_title' => 'SARS Rep Title',
                'sars_rep_gender' => 'SARS Rep Gender',
                'sars_rep_id_number' => 'SARS Rep ID Number',
                'sars_rep_id_type' => 'SARS Rep ID Type',
                'sars_rep_id_issue_date' => 'SARS Rep ID Issue Date',
                'sars_rep_tax_number' => 'SARS Rep Tax Number',
                'sars_rep_position' => 'SARS Rep Position',
                'sars_rep_date_registered' => 'SARS Rep Date Registered',
                'sars_login' => 'SARS Login',
                'sars_password' => 'SARS Password',
                'sars_otp_mobile' => 'SARS OTP Mobile',
                'sars_otp_email' => 'SARS OTP Email',
            ],
            'Banking Details' => [
                'bank_account_holder' => 'Account Holder',
                'bank_account_number' => 'Account Number',
                'bank_account_type' => 'Account Type',
                'bank_name' => 'Bank Name',
                'bank_branch_code' => 'Branch Code',
            ],
            'Director Details' => [
                'director_first_name' => 'Director First Name',
                'director_middle_name' => 'Director Middle Name',
                'director_surname' => 'Director Surname',
                'director_initial' => 'Director Initial',
                'director_title' => 'Director Title',
                'director_gender' => 'Director Gender',
                'director_id_number' => 'Director ID Number',
                'director_id_type' => 'Director ID Type',
                'director_id_issue_date' => 'Director ID Issue Date',
                'director_marital_status' => 'Director Marital Status',
                'director_marriage_type' => 'Director Marriage Type',
                'director_marriage_date' => 'Director Marriage Date',
            ],
            'Partner Details' => [
                'partner_first_name' => 'Partner First Name',
                'partner_middle_name' => 'Partner Middle Name',
                'partner_surname' => 'Partner Surname',
                'partner_title' => 'Partner Title',
                'partner_gender' => 'Partner Gender',
                'partner_id_number' => 'Partner ID Number',
                'partner_id_type' => 'Partner ID Type',
                'partner_id_issue_date' => 'Partner ID Issue Date',
            ],
            'Form Input Fields' => [
                'requested_by' => 'Requested By',
                'prepared_by' => 'Prepared By',
                'approved_by' => 'Approved By',
                'signed_by' => 'Signed By',
                'document_date' => 'Document Date',
            ],
        ];
    }
}
