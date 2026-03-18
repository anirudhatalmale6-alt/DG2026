<?php

namespace Modules\JobCards\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BODocumentService
{
    protected JobCardService $jobCardService;

    public function __construct(JobCardService $jobCardService)
    {
        $this->jobCardService = $jobCardService;
    }

    /**
     * Get full BO data for a client — used by all PDF generators.
     */
    public function getBOData(int $clientId): array
    {
        $client = DB::table('client_master')
            ->where('client_id', $clientId)
            ->first();

        if (!$client) return [];

        $totalShares = (int) ($client->number_of_shares ?? 0);
        $shareType = $client->share_type_name ?: 'Ordinary Shares';

        $directors = DB::table('client_master_directors as d')
            ->leftJoin('cims_persons as p', 'd.person_id', '=', 'p.id')
            ->where('d.client_id', $clientId)
            ->select([
                'd.*',
                'p.id as person_id_ref',
                'p.id_front_image as person_id_front',
                'p.id_back_image as person_id_back',
                'p.passport_image',
                'p.signature_image',
                'p.poa_image',
                'p.poa_uploaded_at',
                'p.tax_number',
                'p.address_line as person_address_line',
                'p.address_line_2 as person_address_line_2',
                'p.suburb as person_suburb',
                'p.city as person_city',
                'p.postal_code as person_postal_code',
                'p.province as person_province',
                'p.address_country as person_country',
                'p.mobile_phone as person_mobile',
                'p.email as person_email',
                'p.office_phone as person_office_phone',
            ])
            ->orderBy('d.firstname')
            ->get();

        // Share certificates
        $certificates = DB::table('cims_share_certificates')
            ->where('client_id', $clientId)
            ->where('is_active', 1)
            ->orderBy('certificate_number')
            ->get();

        // Company settings for firm details
        $companySettings = $this->getCompanySettings();
        $agentCode = $this->getAgentCode();

        return [
            'client'          => $client,
            'totalShares'     => $totalShares,
            'shareType'       => $shareType,
            'directors'       => $directors,
            'certificates'    => $certificates,
            'companySettings' => $companySettings,
            'agentCode'       => $agentCode,
            'generatedAt'     => Carbon::now(),
        ];
    }

    /**
     * Generate Register of Shareholders PDF.
     */
    public function generateShareholderRegister(int $jobCardId, int $clientId): array
    {
        $data = $this->getBOData($clientId);
        $logoBase64 = $this->getLogoBase64();

        $pdf = Pdf::loadView('jobcards::pdf.bo-shareholder-register', array_merge($data, [
            'logoBase64' => $logoBase64,
        ]));
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'sans-serif']);

        return $this->storeBOPdf($pdf, $jobCardId, $data['client'], 'Register_of_Shareholders');
    }

    /**
     * Generate Register of Beneficial Owners PDF.
     */
    public function generateBeneficialOwnerRegister(int $jobCardId, int $clientId): array
    {
        $data = $this->getBOData($clientId);
        $logoBase64 = $this->getLogoBase64();

        $pdf = Pdf::loadView('jobcards::pdf.bo-owner-register', array_merge($data, [
            'logoBase64' => $logoBase64,
        ]));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'sans-serif']);

        return $this->storeBOPdf($pdf, $jobCardId, $data['client'], 'Register_of_Beneficial_Owners');
    }

    /**
     * Generate Beneficial Ownership Diagram PDF.
     */
    public function generateBODiagram(int $jobCardId, int $clientId): array
    {
        $data = $this->getBOData($clientId);
        $logoBase64 = $this->getLogoBase64();

        $pdf = Pdf::loadView('jobcards::pdf.bo-diagram', array_merge($data, [
            'logoBase64' => $logoBase64,
        ]));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'sans-serif']);

        return $this->storeBOPdf($pdf, $jobCardId, $data['client'], 'Beneficial_Ownership_Diagram');
    }

    /**
     * Generate Ordinary Resolution PDF.
     */
    public function generateOrdinaryResolution(int $jobCardId, int $clientId): array
    {
        $data = $this->getBOData($clientId);
        $logoBase64 = $this->getLogoBase64();

        $pdf = Pdf::loadView('jobcards::pdf.bo-ordinary-resolution', array_merge($data, [
            'logoBase64' => $logoBase64,
        ]));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'sans-serif']);

        return $this->storeBOPdf($pdf, $jobCardId, $data['client'], 'Ordinary_Resolution');
    }

    /**
     * Generate CRA01 SARS Proof of Address form for a director.
     */
    public function generateCRA01(int $jobCardId, int $directorId): array
    {
        $director = DB::table('client_master_directors as d')
            ->leftJoin('cims_persons as p', 'd.person_id', '=', 'p.id')
            ->where('d.id', $directorId)
            ->select([
                'd.*',
                'p.tax_number',
                'p.address_line as person_address_line',
                'p.address_line_2 as person_address_line_2',
                'p.suburb as person_suburb',
                'p.city as person_city',
                'p.postal_code as person_postal_code',
                'p.province as person_province',
                'p.address_country as person_country',
                'p.mobile_phone as person_mobile',
                'p.email as person_email',
                'p.office_phone as person_office_phone',
                'p.signature_image',
            ])
            ->first();

        if (!$director) {
            return ['success' => false, 'message' => 'Director not found.'];
        }

        $jobCard = DB::table('cims_job_cards')->where('id', $jobCardId)->first();
        $client = DB::table('client_master')->where('client_id', $jobCard->client_id)->first();

        $pdf = Pdf::loadView('jobcards::pdf.bo-cra01', [
            'director'  => $director,
            'client'    => $client,
            'today'     => Carbon::now(),
        ]);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'sans-serif']);

        $pdfContent = $pdf->output();

        $filename = sprintf(
            'CRA01_%s_%s_%s.pdf',
            $director->firstname,
            $director->surname,
            Carbon::now()->format('Ymd_His')
        );

        $storagePath = base_path('../storage/' . config('job_cards.packs_path', 'job_cards/packs'));
        if (!is_dir($storagePath)) mkdir($storagePath, 0755, true);

        file_put_contents($storagePath . '/' . $filename, $pdfContent);

        // Store as attachment
        DB::table('cims_job_card_attachments')->insert([
            'job_card_id'        => $jobCardId,
            'file_name'          => $filename,
            'file_original_name' => $filename,
            'file_path'          => config('job_cards.packs_path', 'job_cards/packs') . '/' . $filename,
            'file_mime_type'     => 'application/pdf',
            'file_size'          => strlen($pdfContent),
            'file_type'          => 'poa_document',
            'director_id'        => $directorId,
            'document_category'  => 'cra01',
            'uploaded_by'        => Auth::id(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return [
            'success'  => true,
            'filename' => $filename,
            'message'  => 'CRA01 generated for ' . $director->firstname . ' ' . $director->surname,
        ];
    }

    /**
     * Generate all 4 BO documents at once.
     */
    public function generateAllBODocuments(int $jobCardId, int $clientId): array
    {
        $results = [];
        $results['shareholders'] = $this->generateShareholderRegister($jobCardId, $clientId);
        $results['beneficialOwners'] = $this->generateBeneficialOwnerRegister($jobCardId, $clientId);
        $results['diagram'] = $this->generateBODiagram($jobCardId, $clientId);
        $results['resolution'] = $this->generateOrdinaryResolution($jobCardId, $clientId);
        return $results;
    }

    /**
     * Upload POA document for a director.
     */
    public function uploadPOA(int $jobCardId, int $directorId, $file): array
    {
        $storagePath = base_path('../storage/' . config('job_cards.documents_path', 'job_cards/documents'));
        if (!is_dir($storagePath)) mkdir($storagePath, 0755, true);

        $storedName = time() . '_poa_' . $file->getClientOriginalName();
        $file->move($storagePath, $storedName);

        DB::table('cims_job_card_attachments')->insert([
            'job_card_id'        => $jobCardId,
            'file_name'          => $storedName,
            'file_original_name' => $file->getClientOriginalName(),
            'file_path'          => config('job_cards.documents_path', 'job_cards/documents') . '/' . $storedName,
            'file_mime_type'     => $file->getClientMimeType(),
            'file_size'          => $file->getSize(),
            'file_type'          => 'poa_document',
            'director_id'        => $directorId,
            'document_category'  => 'poa',
            'uploaded_by'        => Auth::id(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return ['success' => true, 'message' => 'Proof of Address uploaded.'];
    }

    /**
     * Fetch POA from person record.
     */
    public function fetchPOA(int $jobCardId, int $directorId): array
    {
        $director = DB::table('client_master_directors as d')
            ->leftJoin('cims_persons as p', 'd.person_id', '=', 'p.id')
            ->where('d.id', $directorId)
            ->select(['d.id', 'd.firstname', 'd.surname', 'p.poa_image', 'p.poa_uploaded_at'])
            ->first();

        if (!$director || empty($director->poa_image)) {
            return ['success' => false, 'message' => 'No POA document found on record for ' . ($director->firstname ?? '') . ' ' . ($director->surname ?? '') . '.'];
        }

        // Check freshness (60 days)
        $isFresh = false;
        if ($director->poa_uploaded_at) {
            $isFresh = Carbon::parse($director->poa_uploaded_at)->diffInDays(Carbon::now()) <= 60;
        }

        DB::table('cims_job_card_attachments')->insert([
            'job_card_id'        => $jobCardId,
            'file_name'          => basename($director->poa_image),
            'file_original_name' => $director->firstname . '_' . $director->surname . '_POA.' . pathinfo($director->poa_image, PATHINFO_EXTENSION),
            'file_path'          => $director->poa_image,
            'file_type'          => 'poa_document',
            'file_mime_type'     => 'image/' . pathinfo($director->poa_image, PATHINFO_EXTENSION),
            'director_id'        => $directorId,
            'document_category'  => 'poa',
            'uploaded_by'        => Auth::id(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $msg = 'POA fetched for ' . $director->firstname . ' ' . $director->surname;
        if (!$isFresh) $msg .= ' (WARNING: Document may be older than 60 days)';

        return ['success' => true, 'message' => $msg, 'isFresh' => $isFresh];
    }

    // ─── Helpers ───

    protected function storeBOPdf($pdf, int $jobCardId, $client, string $docType): array
    {
        $pdfContent = $pdf->output();

        $filename = sprintf(
            '%s - %s on %s.pdf',
            $docType,
            $client->company_name ?? 'Unknown',
            Carbon::now()->format('Y-m-d')
        );

        $safeFilename = preg_replace('/[^a-zA-Z0-9\s\-_\.]/', '', $filename);

        $storagePath = base_path('../storage/' . config('job_cards.packs_path', 'job_cards/packs'));
        if (!is_dir($storagePath)) mkdir($storagePath, 0755, true);

        file_put_contents($storagePath . '/' . $safeFilename, $pdfContent);

        $attachmentId = DB::table('cims_job_card_attachments')->insertGetId([
            'job_card_id'        => $jobCardId,
            'file_name'          => $safeFilename,
            'file_original_name' => $filename,
            'file_path'          => config('job_cards.packs_path', 'job_cards/packs') . '/' . $safeFilename,
            'file_mime_type'     => 'application/pdf',
            'file_size'          => strlen($pdfContent),
            'file_type'          => 'bo_document',
            'document_category'  => strtolower(str_replace(' ', '_', $docType)),
            'uploaded_by'        => Auth::id(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return [
            'success'       => true,
            'filename'      => $filename,
            'attachment_id' => $attachmentId,
            'message'       => $docType . ' generated successfully.',
        ];
    }

    protected function getLogoBase64(): ?string
    {
        $logoPath = base_path('../storage/logos/app/cims_inv_logo.png');
        if (file_exists($logoPath)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
        return null;
    }

    protected function getCompanySettings(): array
    {
        $settings = DB::table('settings')->whereIn('settings_id', [1])->first();
        return [
            'company_name' => $settings->settings_company_name ?? '',
            'address'      => $settings->settings_company_address_line_1 ?? '',
            'city'         => $settings->settings_company_city ?? '',
            'state'        => $settings->settings_company_state ?? '',
            'zip'          => $settings->settings_company_zipcode ?? '',
            'country'      => $settings->settings_company_country ?? '',
            'phone'        => $settings->settings_company_telephone ?? '',
            'email'        => $settings->settings_company_email ?? '',
        ];
    }

    protected function getAgentCode(): string
    {
        // CIPC agent code for the firm
        return 'CYMDIM';
    }
}
