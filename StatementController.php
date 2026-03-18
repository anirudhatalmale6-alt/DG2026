<?php

namespace Modules\CustomerStatements\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CustomerStatements\Services\StatementService;
use Modules\CustomerStatements\Services\AgingService;
use Modules\CustomerStatements\Mail\StatementMail;
use Modules\cims_pm_pro\Models\Document;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class StatementController extends Controller
{
    protected StatementService $statementService;
    protected AgingService $agingService;

    public function __construct(StatementService $statementService, AgingService $agingService)
    {
        $this->statementService = $statementService;
        $this->agingService = $agingService;
    }

    /**
     * Show the main statement page with client selector and date range pickers.
     *
     * GET /statements
     */
    public function index()
    {
        $clients = $this->statementService->getClients();

        // Default date range: first day of current month to today
        $defaultFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $defaultTo = Carbon::now()->format('Y-m-d');

        return view('statements::index', [
            'clients' => $clients,
            'defaultFrom' => $defaultFrom,
            'defaultTo' => $defaultTo,
        ]);
    }

    /**
     * Generate a statement for the selected client and date range.
     *
     * POST /statements/generate
     */
    public function generate(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer|exists:clients,client_id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $clientId = (int) $request->input('client_id');
        $fromDate = Carbon::parse($request->input('from_date'))->format('Y-m-d');
        $toDate = Carbon::parse($request->input('to_date'))->format('Y-m-d');

        // Generate the statement data
        $statementData = $this->statementService->generateStatement($clientId, $fromDate, $toDate);

        // Generate aging data
        $agingData = $this->agingService->calculateAging($clientId, $toDate);

        // If AJAX request, return JSON for inline rendering
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(array_merge($statementData, [
                'aging' => $agingData,
            ]));
        }

        // Normal request - return full page
        $clients = $this->statementService->getClients();

        return view('statements::statement', array_merge($statementData, [
            'aging' => $agingData,
            'clients' => $clients,
        ]));
    }

    /**
     * Generate and download a PDF statement.
     *
     * GET /statements/{client_id}/pdf?from=&to=
     */
    public function pdf($clientId, Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $clientId = (int) $clientId;
        $fromDate = Carbon::parse($request->input('from'))->format('Y-m-d');
        $toDate = Carbon::parse($request->input('to'))->format('Y-m-d');

        // Generate the statement data
        $statementData = $this->statementService->generateStatement($clientId, $fromDate, $toDate);

        // Generate aging data
        $agingData = $this->agingService->calculateAging($clientId, $toDate);

        // Load logo as base64 for embedding in PDF
        $logoBase64 = $this->getLogoBase64();
        $bankingBase64 = $this->getBankingBase64();

        $data = array_merge($statementData, [
            'aging' => $agingData,
            'logoBase64' => $logoBase64,
            'bankingBase64' => $bankingBase64,
        ]);

        $pdf = Pdf::loadView('statements::pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        $clientName = $statementData['client']->client_company_name ?? 'Client';
        $filename = 'Statement_' . str_replace(' ', '_', $clientName) . '_' . $fromDate . '_to_' . $toDate . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Email the statement to the client.
     *
     * POST /statements/{client_id}/email
     */
    public function email($clientId, Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $clientId = (int) $clientId;
        $fromDate = Carbon::parse($request->input('from_date'))->format('Y-m-d');
        $toDate = Carbon::parse($request->input('to_date'))->format('Y-m-d');

        // Get the client email
        $clientEmail = $this->statementService->getClientEmail($clientId);

        if (empty($clientEmail)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'No email address found for this client.'], 422);
            }
            return redirect()->back()->with('error', 'No email address found for this client. Please add an email address to the client account owner.');
        }

        // Generate the statement data
        $statementData = $this->statementService->generateStatement($clientId, $fromDate, $toDate);
        $agingData = $this->agingService->calculateAging($clientId, $toDate);

        // Load logo as base64
        $logoBase64 = $this->getLogoBase64();
        $bankingBase64 = $this->getBankingBase64();

        $data = array_merge($statementData, [
            'aging' => $agingData,
            'logoBase64' => $logoBase64,
            'bankingBase64' => $bankingBase64,
        ]);

        // Generate the PDF
        $pdf = Pdf::loadView('statements::pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        $clientName = $statementData['client']->client_company_name ?? 'Client';
        $filename = 'Statement_' . str_replace(' ', '_', $clientName) . '_' . $fromDate . '_to_' . $toDate . '.pdf';
        $pdfContent = $pdf->output();

        // Format period for subject
        $periodFrom = Carbon::parse($fromDate)->format('d-m-Y');
        $periodTo = Carbon::parse($toDate)->format('d-m-Y');
        $companyName = $statementData['settings']['settings_company_name'];

        // Send the email
        Mail::to($clientEmail)->send(new StatementMail(
            $pdfContent,
            $filename,
            $clientName,
            $companyName,
            $periodFrom,
            $periodTo,
            $statementData['closing_balance'],
            $statementData['settings']['settings_system_currency_symbol'] ?? 'R '
        ));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Statement emailed successfully to ' . $clientEmail]);
        }

        return redirect()->back()->with('success', 'Statement emailed successfully to ' . $clientEmail);
    }

    /**
     * Generate PDF, store in document system, and return document info.
     * Cloned from CIMS_EMP201 module's generateAndStoreStatementPdf pattern.
     */
    private function generateAndStoreStatementPdf($clientId, $fromDate, $toDate)
    {
        // Get statement data using the same service methods
        $statementData = $this->statementService->generateStatement($clientId, $fromDate, $toDate);
        $agingData = $this->agingService->calculateAging($clientId, $toDate);
        $logoBase64 = $this->getLogoBase64();
        $bankingBase64 = $this->getBankingBase64();

        $data = array_merge($statementData, [
            'aging' => $agingData,
            'logoBase64' => $logoBase64,
            'bankingBase64' => $bankingBase64,
        ]);

        $clientCode = $statementData['client_code'] ?: 'STMT';
        $clientName = $statementData['client']->client_company_name ?? 'Client';

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('statements::pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);
        $pdfContent = $pdf->output();

        // Generate stored filename using same convention as EMPSA module
        $docType = 'Customer Statement of Account';
        $storedFilename = Document::generateStoredFilename($clientCode, $docType, 'pdf');

        // Save to document storage: client_master_docs/{CLIENT_CODE}/
        // base_path('../storage/') resolves to public_html/storage/ on this server
        $storagePath = 'client_master_docs/' . $clientCode;
        $fullDir = base_path('../storage/' . $storagePath);
        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0755, true);
        }
        $fullFilePath = $fullDir . '/' . $storedFilename;
        file_put_contents($fullFilePath, $pdfContent);
        $fileSize = strlen($pdfContent);

        // Format period for description
        $periodFrom = Carbon::parse($fromDate)->format('d/m/Y');
        $periodTo = Carbon::parse($toDate)->format('d/m/Y');

        // Create document record in cims_documents table (same pattern as EMPSA)
        $document = Document::create([
            'client_id'          => $clientId,
            'client_code'        => $clientCode,
            'client_name'        => $clientName,
            'title'              => $storedFilename,
            'document_ref'       => 'STMT',
            'document_code'      => 'CUST_SOA',
            'doc_group'          => 'Customer Statements',
            'file_original_name' => 'Statement_' . str_replace(' ', '_', $clientName) . '_' . $fromDate . '_to_' . $toDate . '.pdf',
            'file_stored_name'   => $storedFilename,
            'file_path'          => $storagePath . '/' . $storedFilename,
            'file_mime_type'     => 'application/pdf',
            'description'        => 'Customer Statement of Account for ' . $clientName . ' - Period ' . $periodFrom . ' to ' . $periodTo,
            'uploaded_by'        => Auth::check() ? Auth::user()->name : 'System',
            'created_by'         => Auth::check() ? Auth::user()->id : null,
            'status'             => 1,
            'show_as_current'    => true,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Set file_size directly (not in fillable but column exists)
        DB::table('cims_documents')->where('id', $document->id)->update(['file_size' => $fileSize]);

        return [
            'document'    => $document,
            'file_path'   => $fullFilePath,
            'file_name'   => $storedFilename,
            'data'        => $data,
        ];
    }

    /**
     * Generate PDF and redirect to document viewer.
     * Called when user clicks the pink PDF button.
     * Cloned from CIMS_EMP201 module's generateStatementPdf pattern.
     */
    public function generateStatementPdf(Request $request)
    {
        $clientId = $request->get('client_id');
        $fromDate = $request->get('from_date');
        $toDate   = $request->get('to_date');

        if (!$clientId || !$fromDate || !$toDate) {
            return response()->json(['error' => 'Client and date range are required.'], 400);
        }

        try {
            $result = $this->generateAndStoreStatementPdf($clientId, $fromDate, $toDate);
            $document = $result['document'];

            return response()->json([
                'success'     => true,
                'document_id' => $document->id,
                'view_url'    => route('cimsdocmanager.view', $document->id),
                'message'     => 'PDF generated and stored successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Load the company logo as a base64 string for PDF embedding.
     *
     * @return string|null
     */
    protected function getLogoBase64(): ?string
    {
        $logoPath = base_path('../storage/logos/app/cims_inv_logo.png');

        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            return 'data:image/png;base64,' . base64_encode($logoData);
        }

        return null;
    }

    /**
     * Load the banking details image as a base64 string for PDF embedding.
     *
     * @return string|null
     */
    protected function getBankingBase64(): ?string
    {
        $bankingPath = base_path('../storage/logos/app/banking_atp.png');

        if (file_exists($bankingPath)) {
            $bankingData = file_get_contents($bankingPath);
            return 'data:image/png;base64,' . base64_encode($bankingData);
        }

        return null;
    }
}
