<?php

namespace Modules\CustomerAgedAnalysis\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\CustomerAgedAnalysis\Services\AgedAnalysisService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AgedAnalysisController extends Controller
{
    protected AgedAnalysisService $service;

    public function __construct(AgedAnalysisService $service)
    {
        $this->service = $service;
    }

    /**
     * Show the Aged Analysis report page.
     *
     * GET /aged-analysis
     */
    public function index()
    {
        $defaultDate = Carbon::today()->format('Y-m-d');

        return view('agedanalysis::index', [
            'defaultDate' => $defaultDate,
        ]);
    }

    /**
     * Generate the aged analysis report data.
     *
     * POST /aged-analysis/generate
     */
    public function generate(Request $request)
    {
        $request->validate([
            'as_of_date' => 'nullable|date',
        ]);

        $asOfDate = $request->input('as_of_date')
            ? Carbon::parse($request->input('as_of_date'))->format('Y-m-d')
            : null;

        $reportData = $this->service->generateReport($asOfDate);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($reportData);
        }

        return view('agedanalysis::index', array_merge($reportData, [
            'defaultDate' => $asOfDate ?? Carbon::today()->format('Y-m-d'),
        ]));
    }

    /**
     * Generate and stream PDF download.
     *
     * POST /aged-analysis/pdf
     */
    public function pdf(Request $request)
    {
        $asOfDate = $request->input('as_of_date')
            ? Carbon::parse($request->input('as_of_date'))->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');

        $mode = $request->input('mode', 'summary');

        $reportData = $this->service->generateReport($asOfDate);

        $logoBase64 = $this->getLogoBase64();

        $data = array_merge($reportData, [
            'logoBase64' => $logoBase64,
            'mode' => $mode,
        ]);

        $pdf = Pdf::loadView('agedanalysis::pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        $filename = 'Customer_Aged_Analysis_' . $asOfDate . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Email the aged analysis PDF using CIMS_Email SMTP settings (immediate delivery).
     *
     * POST /aged-analysis/email
     */
    public function email(Request $request)
    {
        $request->validate([
            'email_to' => 'required|email',
            'as_of_date' => 'nullable|date',
        ]);

        $emailTo = $request->input('email_to');
        $subject = $request->input('subject', 'Customer Aged Analysis Report');
        $asOfDate = $request->input('as_of_date')
            ? Carbon::parse($request->input('as_of_date'))->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');

        $mode = $request->input('mode', 'summary');

        // Load CIMS_Email SMTP settings for immediate delivery
        $this->loadSmtpSettings();

        $reportData = $this->service->generateReport($asOfDate);
        $logoBase64 = $this->getLogoBase64();

        $data = array_merge($reportData, [
            'logoBase64' => $logoBase64,
            'mode' => $mode,
        ]);

        $pdf = Pdf::loadView('agedanalysis::pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        $pdfContent = $pdf->output();
        $filename = 'Customer_Aged_Analysis_' . $asOfDate . '.pdf';
        $companyName = $reportData['settings']['settings_company_name'] ?? '';
        $fromEmail = Config::get('mail.from.address');
        $fromName = Config::get('mail.from.name', $companyName);

        // Build email body with signature
        $signature = $this->getUserSignature();
        $signatureHtml = $this->buildSignatureHtml($signature);
        $disclaimerHtml = $this->buildDisclaimerHtml();

        $bodyHtml = '<!DOCTYPE html><html><head><meta charset="utf-8"></head>'
            . '<body style="margin:0;padding:0;width:100%;font-family:Arial,sans-serif;">'
            . '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="width:100%;"><tr><td style="padding:15px;">'
            . '<p style="font-size:14px;color:#333;">Please find attached the <strong>Customer Aged Analysis Report</strong> as of '
            . Carbon::parse($asOfDate)->format('d/m/Y') . '.</p>'
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

        return response()->json([
            'success' => true,
            'message' => 'Aged Analysis report emailed successfully to ' . $emailTo,
        ]);
    }

    /**
     * Load SMTP settings from cims_email_settings table and override Laravel mail config.
     * Same pattern as CIMS_Email module — sends immediately, bypasses queue.
     */
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

            // Purge cached SMTP mailer so it picks up the new settings
            app('mail.manager')->purge('smtp');
        }
    }

    /**
     * Load the company logo as a base64 string for PDF embedding.
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
     * Get the current user's email signature from cims_email_signatures table.
     */
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

    /**
     * Build disclaimer HTML from global email settings.
     */
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

    /**
     * Build signature HTML from the user's signature record.
     * Same pattern as CIMS_Email module for consistency.
     */
    private function buildSignatureHtml($signature)
    {
        if (!$signature) return '';

        // If user has custom HTML signature, use that
        if (!empty($signature->signature_html)) {
            return $signature->signature_html;
        }

        // Auto-generate from fields
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

        // Get banner image URL from global settings
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

        // Outer wrapper table: signature left, stamp right
        $html = '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:Arial,sans-serif;font-size:13px;color:#333;border-collapse:collapse;width:100%;">';
        $html .= '<tr><td style="vertical-align:top;">';
        $html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:Arial,sans-serif;font-size:13px;color:#333;border-collapse:collapse;width:100%;">';

        // Name + Title with pink accent line
        $html .= '<tr><td style="padding-bottom:10px;border-bottom:3px solid #E91E8C;">';
        $html .= '<strong style="font-size:16px;color:#1a1a2e;letter-spacing:0.5px;">' . htmlspecialchars($name) . '</strong>';
        if ($title) $html .= '<br><span style="font-size:12px;color:#777;margin-top:2px;display:inline-block;">' . htmlspecialchars($title) . '</span>';
        $html .= '</td></tr>';

        // Contact numbers row
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

        // Divider
        $html .= '<tr><td style="padding:0;"><div style="border-top:1px solid #eee;"></div></td></tr>';

        // Company row
        if ($company) {
            $html .= '<tr><td style="padding-top:10px;">';
            $html .= '<strong style="font-size:14px;color:#1a1a2e;">' . htmlspecialchars($company) . '</strong>';
            if ($website) {
                $html .= ' <span style="color:#ccc;">|</span> <a href="' . $websiteUrl . '" style="font-size:13px;color:#0066CC;text-decoration:none;">' . htmlspecialchars($website) . '</a>';
            }
            $html .= '</td></tr>';
        }

        // Slogan row
        if ($slogan) {
            $html .= '<tr><td style="padding-top:4px;">';
            $html .= '<em style="font-size:12px;color:#E91E8C;font-style:italic;letter-spacing:0.3px;">' . htmlspecialchars($slogan) . '</em>';
            $html .= '</td></tr>';
        }

        // Banner image
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

        // Separator + logo row
        $html .= '<tr><td colspan="2" style="padding-top:14px;"><div style="border-top:1px solid #e0e0e0;"></div></td></tr>';

        $logoLinkStart = $websiteUrl ? '<a href="' . $websiteUrl . '" target="_blank" style="text-decoration:none;">' : '';
        $logoLinkEnd = $websiteUrl ? '</a>' : '';
        $html .= '<tr><td colspan="2" style="padding-top:10px;text-align:left;">';
        $html .= $logoLinkStart;
        $html .= '<img src="' . $logoUrl . '" alt="CIMS" style="max-width:280px;height:auto;pointer-events:none;-webkit-user-drag:none;user-select:none;-moz-user-select:none;-webkit-user-select:none;-ms-user-select:none;" draggable="false" oncontextmenu="return false;" ondragstart="return false;">';
        $html .= $logoLinkEnd;
        $html .= '</td></tr>';

        $html .= '</table>';

        return $html;
    }
}
