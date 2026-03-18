<?php

namespace Modules\JobCards\Services;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PackGeneratorService
{
    /**
     * Generate a PDF pack (internal or external).
     *
     * @param array  $jobCardData Full job card data from JobCardService::getJobCard()
     * @param string $type        'internal' or 'external'
     * @return array ['pdf' => PDF content, 'filename' => string, 'path' => stored path]
     */
    public function generatePack(array $jobCardData, string $type = 'internal'): array
    {
        $jobCard = $jobCardData['jobCard'];
        $client = $jobCardData['client'];
        $jobType = $jobCardData['jobType'];

        $logoBase64 = $this->getLogoBase64();
        $companySettings = $this->getCompanySettings();

        $viewData = array_merge($jobCardData, [
            'packType'        => $type,
            'logoBase64'      => $logoBase64,
            'companySettings' => $companySettings,
            'generatedAt'     => Carbon::now()->format('d/m/Y H:i'),
            'generatedBy'     => auth()->user()->first_name ?? 'System',
        ]);

        $viewName = $type === 'external'
            ? 'jobcards::pdf.external-pack'
            : 'jobcards::pdf.internal-pack';

        $pdf = Pdf::loadView($viewName, $viewData);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'sans-serif',
        ]);

        $pdfContent = $pdf->output();

        // Store the pack
        $filename = sprintf(
            '%s_%s_%s_%s.pdf',
            $jobCard->job_code,
            $type,
            $client->client_code ?? 'UNKNOWN',
            Carbon::now()->format('Ymd_His')
        );

        $storagePath = base_path('../storage/' . config('job_cards.packs_path', 'job_cards/packs'));
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $filePath = $storagePath . '/' . $filename;
        file_put_contents($filePath, $pdfContent);

        // Update job card with pack path
        $column = $type === 'external' ? 'external_pack_path' : 'internal_pack_path';
        DB::table('cims_job_cards')
            ->where('id', $jobCard->id)
            ->update([
                $column     => config('job_cards.packs_path', 'job_cards/packs') . '/' . $filename,
                'updated_at' => now(),
            ]);

        // Record attachment
        DB::table('cims_job_card_attachments')->insert([
            'job_card_id'        => $jobCard->id,
            'file_name'          => $filename,
            'file_original_name' => $filename,
            'file_path'          => config('job_cards.packs_path', 'job_cards/packs') . '/' . $filename,
            'file_mime_type'     => 'application/pdf',
            'file_size'          => strlen($pdfContent),
            'file_type'          => $type . '_pack',
            'uploaded_by'        => auth()->id(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // For internal pack, also store in cims_documents
        if ($type === 'internal') {
            $this->storeInCimsDocuments($jobCard, $client, $filename, $filePath, $pdfContent);
        }

        return [
            'pdf'      => $pdfContent,
            'filename' => $filename,
            'path'     => $filePath,
        ];
    }

    /**
     * Store internal pack in cims_documents for the client.
     */
    protected function storeInCimsDocuments($jobCard, $client, string $filename, string $filePath, string $pdfContent): void
    {
        try {
            DB::table('cims_documents')->insert([
                'title'              => 'Job Card Pack - ' . $jobCard->job_code,
                'document_code'      => 'JC-' . $jobCard->job_code,
                'client_id'          => $client->client_id,
                'client_name'        => $client->company_name,
                'client_code'        => $client->client_code ?? '',
                'file_original_name' => $filename,
                'file_stored_name'   => $filename,
                'file_mime_type'     => 'application/pdf',
                'file_path'          => config('job_cards.packs_path', 'job_cards/packs') . '/' . $filename,
                'doc_group'          => 'Job Cards',
                'status'             => 'Current',
                'show_as_current'    => 1,
                'is_archived'        => 0,
                'is_trashed'         => 0,
                'uploaded_by'        => auth()->id(),
                'created_by'         => auth()->id(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        } catch (\Exception $e) {
            // Don't fail the pack generation if cims_documents insert fails
            \Log::warning('Failed to store job card pack in cims_documents: ' . $e->getMessage());
        }
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
        $settings = DB::table('settings')
            ->whereIn('settings_id', [1])
            ->first();

        return [
            'company_name' => $settings->settings_company_name ?? '',
            'address'      => $settings->settings_company_address_line_1 ?? '',
            'city'         => $settings->settings_company_city ?? '',
            'state'        => $settings->settings_company_state ?? '',
            'zip'          => $settings->settings_company_zip ?? '',
            'country'      => $settings->settings_company_country ?? '',
            'phone'        => $settings->settings_company_telephone ?? '',
            'email'        => $settings->settings_company_email ?? '',
        ];
    }
}
