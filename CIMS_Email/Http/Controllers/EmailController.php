<?php

namespace Modules\CIMS_Email\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailController extends Controller
{
    /**
     * Boot SMTP settings from database on each request
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->loadSmtpSettings();
            return $next($request);
        });
    }

    /**
     * Load SMTP settings from DB and apply to mail config
     */
    private function loadSmtpSettings()
    {
        try {
            $settings = DB::table('cims_email_settings')->pluck('setting_value', 'setting_key')->toArray();
            if (!empty($settings['smtp_host'])) {
                Config::set('mail.default', 'smtp');
                Config::set('mail.mailers.smtp.host', $settings['smtp_host']);
                Config::set('mail.mailers.smtp.port', $settings['smtp_port'] ?? 587);
                Config::set('mail.mailers.smtp.encryption', $settings['smtp_encryption'] ?? 'tls');
                Config::set('mail.mailers.smtp.username', $settings['smtp_username'] ?? '');
                Config::set('mail.mailers.smtp.password', $settings['smtp_password'] ?? '');
                Config::set('mail.from.address', $settings['from_email'] ?? $settings['smtp_username']);
                Config::set('mail.from.name', $settings['from_name'] ?? 'SmartWeigh CIMS');
            }
        } catch (\Exception $e) {
            // Table may not exist yet - silently continue
        }
    }

    /**
     * Get folder counts for sidebar
     */
    private function getFolderCounts()
    {
        return [
            'sent' => DB::table('cims_emails')->where('user_id', Auth::id())->where('folder', 'sent')->whereNull('deleted_at')->count(),
            'drafts' => DB::table('cims_emails')->where('user_id', Auth::id())->where('folder', 'drafts')->whereNull('deleted_at')->count(),
            'trash' => DB::table('cims_emails')->where('user_id', Auth::id())->where('folder', 'trash')->whereNull('deleted_at')->count(),
        ];
    }

    /**
     * Email Dashboard - shows sent emails (default view)
     */
    public function index(Request $request)
    {
        $folder = $request->get('folder', 'sent');
        $search = $request->get('search', '');
        $clientFilter = $request->get('client_id');

        $query = DB::table('cims_emails')
            ->where('user_id', Auth::id())
            ->where('folder', $folder)
            ->whereNull('deleted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('to_emails', 'like', "%{$search}%")
                  ->orWhere('body_text', 'like', "%{$search}%");
            });
        }

        if ($clientFilter) {
            $query->where('client_id', $clientFilter);
        }

        $emails = $query->orderByDesc('created_at')->paginate(20);

        $clients = DB::table('client_master')
            ->where('is_active', 1)
            ->orderBy('company_name')
            ->get(['client_id', 'client_code', 'company_name']);

        $counts = $this->getFolderCounts();

        return view('cims_email::emails.index', compact('emails', 'folder', 'search', 'clients', 'clientFilter', 'counts'));
    }

    /**
     * Compose new email
     */
    public function compose(Request $request)
    {
        $showAll = $request->get('show_all', 0);
        $clientQuery = DB::table('client_master')->orderBy('company_name');
        if (!$showAll) {
            $clientQuery->where('is_active', 1);
        }
        $clients = $clientQuery->get(['client_id', 'client_code', 'company_name', 'is_active']);

        $templates = DB::table('cims_email_templates')
            ->where('is_active', 1)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $draft = null;
        if ($request->has('draft_id')) {
            $draft = DB::table('cims_emails')
                ->where('id', $request->get('draft_id'))
                ->where('user_id', Auth::id())
                ->where('folder', 'drafts')
                ->first();
        }

        $selectedClientId = $request->get('client_id') ?? ($draft->client_id ?? null);
        $counts = $this->getFolderCounts();

        // Get user's signature and disclaimer
        $signature = $this->getUserSignature();
        $signatureHtml = $this->buildSignatureHtml($signature);
        $disclaimerHtml = $this->buildDisclaimerHtml($signature);

        // FROM address from SMTP settings
        $settings = [];
        try {
            $settings = DB::table('cims_email_settings')->pluck('setting_value', 'setting_key')->toArray();
        } catch (\Exception $e) {}
        $fromEmail = $settings['from_email'] ?? ($settings['smtp_username'] ?? '');
        $fromName = $settings['from_name'] ?? 'SmartWeigh CIMS';

        // Recent contacts (last 10 unique emails from sent emails)
        $recentContacts = [];
        try {
            $recentEmails = DB::table('cims_emails')
                ->where('user_id', Auth::id())
                ->where('folder', 'sent')
                ->whereNotNull('to_emails')
                ->orderByDesc('sent_at')
                ->limit(50)
                ->pluck('to_emails');

            $seen = [];
            foreach ($recentEmails as $toJson) {
                $emails = json_decode($toJson, true) ?: [];
                foreach ($emails as $email) {
                    $email = trim($email);
                    if ($email && !isset($seen[$email]) && count($seen) < 10) {
                        $seen[$email] = true;
                        // Try to find name from contacts
                        $contact = DB::table('cims_master_contacts')
                            ->where('email', $email)
                            ->where('is_active', 1)
                            ->first(['first_name', 'last_name', 'known_as']);
                        $recentContacts[] = [
                            'email' => $email,
                            'name' => $contact ? trim($contact->first_name . ' ' . $contact->last_name) : $email,
                            'known_as' => $contact->known_as ?? '',
                        ];
                    }
                }
            }
        } catch (\Exception $e) {}

        return view('cims_email::emails.compose', compact(
            'clients', 'templates', 'draft', 'selectedClientId', 'counts',
            'signatureHtml', 'disclaimerHtml', 'fromEmail', 'fromName',
            'recentContacts', 'showAll'
        ));
    }

    /**
     * Send email
     */
    public function send(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'to_emails' => 'required|string',
            'subject' => 'required|string|max:500',
            'body_html' => 'required|string',
        ]);

        $toEmails = array_map('trim', explode(',', $request->to_emails));
        $ccEmails = $request->cc_emails ? array_map('trim', explode(',', $request->cc_emails)) : [];
        $bccEmails = $request->bcc_emails ? array_map('trim', explode(',', $request->bcc_emails)) : [];

        $user = Auth::user();
        $fromEmail = config('mail.from.address', $user->email ?? 'noreply@smartweigh.co.za');
        $fromName = config('mail.from.name', trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'SmartWeigh');

        // Auto-append disclaimer to email body
        $signature = $this->getUserSignature();
        $disclaimerHtml = $this->buildDisclaimerHtml($signature);
        $bodyHtml = $request->body_html;
        if ($disclaimerHtml) {
            $bodyHtml .= $disclaimerHtml;
        }

        // Store the email record
        $emailId = DB::table('cims_emails')->insertGetId([
            'client_id' => $request->client_id ?: null,
            'user_id' => Auth::id(),
            'from_email' => $fromEmail,
            'from_name' => $fromName,
            'to_emails' => json_encode($toEmails),
            'cc_emails' => json_encode($ccEmails),
            'bcc_emails' => json_encode($bccEmails),
            'subject' => $request->subject,
            'body_html' => $bodyHtml,
            'body_text' => strip_tags($bodyHtml),
            'status' => 'sending',
            'folder' => 'sent',
            'is_read' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('email_attachments/' . $emailId, $filename, 'public');

                DB::table('cims_email_attachments')->insert([
                    'email_id' => $emailId,
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'created_at' => now(),
                ]);
            }
        }

        // Send the email
        try {
            $attachments = DB::table('cims_email_attachments')
                ->where('email_id', $emailId)
                ->get();

            Mail::html($bodyHtml, function ($message) use ($toEmails, $ccEmails, $bccEmails, $fromEmail, $fromName, $request, $attachments) {
                $message->from($fromEmail, $fromName);
                $message->to($toEmails);
                if (!empty($ccEmails)) $message->cc($ccEmails);
                if (!empty($bccEmails)) $message->bcc($bccEmails);
                $message->subject($request->subject);

                foreach ($attachments as $att) {
                    $fullPath = storage_path('app/public/' . $att->file_path);
                    if (file_exists($fullPath)) {
                        $message->attach($fullPath, ['as' => $att->original_filename, 'mime' => $att->mime_type]);
                    }
                }
            });

            DB::table('cims_emails')->where('id', $emailId)->update([
                'status' => 'sent',
                'sent_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->draft_id) {
                DB::table('cims_emails')->where('id', $request->draft_id)->update([
                    'deleted_at' => now(),
                ]);
            }

            return redirect()->route('cimsemail.sent')
                ->with('success', 'Email sent successfully!');

        } catch (\Exception $e) {
            DB::table('cims_emails')->where('id', $emailId)->update([
                'status' => 'failed',
                'updated_at' => now(),
            ]);

            return back()->withInput()
                ->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Save as draft
     */
    public function saveDraft(Request $request)
    {
        $data = [
            'client_id' => $request->client_id ?: null,
            'user_id' => Auth::id(),
            'from_email' => $request->from_email ?: '',
            'from_name' => '',
            'to_emails' => json_encode($request->to_emails ? array_map('trim', explode(',', $request->to_emails)) : []),
            'cc_emails' => json_encode($request->cc_emails ? array_map('trim', explode(',', $request->cc_emails)) : []),
            'bcc_emails' => json_encode($request->bcc_emails ? array_map('trim', explode(',', $request->bcc_emails)) : []),
            'subject' => $request->subject ?? '',
            'body_html' => $request->body_html ?? '',
            'body_text' => strip_tags($request->body_html ?? ''),
            'status' => 'draft',
            'folder' => 'drafts',
            'is_read' => 1,
            'updated_at' => now(),
        ];

        if ($request->draft_id) {
            DB::table('cims_emails')->where('id', $request->draft_id)->where('user_id', Auth::id())->update($data);
            $emailId = $request->draft_id;
        } else {
            $data['created_at'] = now();
            $emailId = DB::table('cims_emails')->insertGetId($data);
        }

        return redirect()->route('cimsemail.compose', ['draft_id' => $emailId])
            ->with('success', 'Draft saved.');
    }

    /**
     * View sent emails
     */
    public function sent(Request $request)
    {
        $request->merge(['folder' => 'sent']);
        return $this->index($request);
    }

    /**
     * View drafts
     */
    public function drafts(Request $request)
    {
        $request->merge(['folder' => 'drafts']);
        return $this->index($request);
    }

    /**
     * View single email
     */
    public function view($id)
    {
        $email = DB::table('cims_emails')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->first();

        if (!$email) abort(404);

        DB::table('cims_emails')->where('id', $id)->update(['is_read' => 1]);

        $attachments = DB::table('cims_email_attachments')
            ->where('email_id', $id)
            ->get();

        $client = null;
        if ($email->client_id) {
            $client = DB::table('client_master')->where('client_id', $email->client_id)->first(['client_id', 'client_code', 'company_name']);
        }

        $counts = $this->getFolderCounts();

        return view('cims_email::emails.view', compact('email', 'attachments', 'client', 'counts'));
    }

    /**
     * Move to trash
     */
    public function trash($id)
    {
        DB::table('cims_emails')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['folder' => 'trash', 'updated_at' => now()]);

        return back()->with('success', 'Email moved to trash.');
    }

    /**
     * Permanently delete
     */
    public function delete($id)
    {
        DB::table('cims_emails')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['deleted_at' => now()]);

        return back()->with('success', 'Email deleted.');
    }

    /**
     * Email templates management
     */
    public function templates()
    {
        $templates = DB::table('cims_email_templates')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $counts = $this->getFolderCounts();

        return view('cims_email::emails.templates', compact('templates', 'counts'));
    }

    /**
     * Store new template
     */
    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'subject' => 'required|string|max:500',
            'body_html' => 'required|string',
            'category' => 'required|string|max:100',
        ]);

        DB::table('cims_email_templates')->insert([
            'name' => $request->name,
            'subject' => $request->subject,
            'body_html' => $request->body_html,
            'category' => $request->category,
            'is_active' => 1,
            'created_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('cimsemail.templates')->with('success', 'Template created.');
    }

    /**
     * Update template
     */
    public function updateTemplate(Request $request, $id)
    {
        DB::table('cims_email_templates')->where('id', $id)->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'body_html' => $request->body_html,
            'category' => $request->category,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'updated_at' => now(),
        ]);

        return redirect()->route('cimsemail.templates')->with('success', 'Template updated.');
    }

    /**
     * Delete template
     */
    public function deleteTemplate($id)
    {
        DB::table('cims_email_templates')->where('id', $id)->delete();
        return back()->with('success', 'Template deleted.');
    }

    /**
     * Load template (AJAX)
     */
    public function loadTemplate($id)
    {
        $template = DB::table('cims_email_templates')->where('id', $id)->first();
        if (!$template) return response()->json(['error' => 'Template not found'], 404);
        return response()->json($template);
    }

    /**
     * Get client contacts (AJAX) - from cims_master_contacts
     */
    public function getClientContacts($clientId)
    {
        $contacts = DB::table('cims_master_contacts')
            ->where('client_id', $clientId)
            ->where('is_active', 1)
            ->orderByDesc('is_primary')
            ->orderBy('first_name')
            ->get(['id', 'title', 'first_name', 'last_name', 'known_as', 'email', 'phone', 'mobile', 'whatsapp', 'position', 'gender', 'photo', 'source']);

        $client = DB::table('client_master')
            ->where('client_id', $clientId)
            ->first(['client_id', 'client_code', 'company_name', 'trading_name', 'tax_number']);

        return response()->json([
            'contacts' => $contacts,
            'client' => $client,
        ]);
    }

    /**
     * SMTP Settings page
     */
    public function settings()
    {
        $settings = [];
        try {
            $settings = DB::table('cims_email_settings')->pluck('setting_value', 'setting_key')->toArray();
        } catch (\Exception $e) {
            // Table may not exist
        }

        $counts = $this->getFolderCounts();

        return view('cims_email::emails.settings', compact('settings', 'counts'));
    }

    /**
     * Save SMTP Settings
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|string',
            'smtp_encryption' => 'nullable|string',
            'smtp_username' => 'required|string',
            'smtp_password' => 'required|string',
            'from_email' => 'required|email',
            'from_name' => 'required|string',
            'disclaimer_html' => 'nullable|string',
            'signature_banner' => 'nullable|image|max:2048',
            'signature_banner_url' => 'nullable|string',
        ]);

        // Handle banner image upload
        if ($request->hasFile('signature_banner')) {
            $file = $request->file('signature_banner');
            $filename = 'email_signature_banner_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(base_path('../storage/email_assets'), $filename);
            $bannerUrl = url('/storage/email_assets/' . $filename);
            $request->merge(['signature_banner_url' => $bannerUrl]);
        }

        $keys = ['smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password', 'from_email', 'from_name', 'disclaimer_html', 'signature_banner_url'];

        foreach ($keys as $key) {
            DB::table('cims_email_settings')->updateOrInsert(
                ['setting_key' => $key],
                ['setting_value' => $request->input($key, ''), 'updated_at' => now()]
            );
        }

        return redirect()->route('cimsemail.settings')->with('success', 'SMTP settings saved successfully!');
    }

    /**
     * Test SMTP connection (AJAX)
     */
    public function testConnection(Request $request)
    {
        try {
            $smtpHost = $request->input('smtp_host', '');
            $smtpPort = $request->input('smtp_port', '587');
            $smtpEncryption = $request->input('smtp_encryption', '');
            $smtpUsername = $request->input('smtp_username', '');
            $smtpPassword = $request->input('smtp_password', '');
            $fromEmail = $request->input('from_email', '') ?: $smtpUsername;
            $fromName = $request->input('from_name', '') ?: 'SmartWeigh CIMS';

            if (empty($smtpHost) || empty($smtpUsername) || empty($smtpPassword)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fill in all required SMTP fields before testing.'
                ]);
            }

            // Temporarily configure SMTP
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', $smtpHost);
            Config::set('mail.mailers.smtp.port', (int) $smtpPort);
            Config::set('mail.mailers.smtp.encryption', $smtpEncryption ?: null);
            Config::set('mail.mailers.smtp.username', $smtpUsername);
            Config::set('mail.mailers.smtp.password', $smtpPassword);

            // Purge the smtp mailer to force re-creation with new config
            app('mail.manager')->purge('smtp');

            // Send a test email to the from address
            Mail::raw('This is a test email from CIMS Email Module. If you receive this, your SMTP settings are working correctly!', function ($message) use ($fromEmail, $fromName) {
                $message->from($fromEmail, $fromName);
                $message->to($fromEmail);
                $message->subject('CIMS Email - SMTP Test (' . now()->format('d M Y H:i') . ')');
            });

            return response()->json([
                'success' => true,
                'message' => 'SMTP connection successful! A test email was sent to ' . $fromEmail
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'SMTP connection failed: ' . $e->getMessage()
            ]);
        }
    }

    // =========================================================================
    // EMAIL SIGNATURES (Per User)
    // =========================================================================

    /**
     * Get current user's signature from DB
     */
    private function getUserSignature()
    {
        try {
            return DB::table('cims_email_signatures')
                ->where('user_id', Auth::id())
                ->where('is_active', 1)
                ->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Build disclaimer HTML from global settings
     */
    private function buildDisclaimerHtml($signature = null)
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
     * Build signature HTML from signature record
     */
    private function buildSignatureHtml($signature)
    {
        if (!$signature) return '';

        // If user has custom HTML, use that
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

        // CIMS Logo URL (from CIMSCore assets)
        $logoUrl = url('/assets/cims_core/atp_cims_logo.jpg');
        $websiteUrl = '';
        if ($website) {
            $websiteUrl = $website;
            if (!preg_match('/^https?:\/\//', $websiteUrl)) $websiteUrl = 'https://' . $websiteUrl;
        }

        $html = '<table cellpadding="0" cellspacing="0" style="font-family:Arial,sans-serif;font-size:13px;color:#333;border-collapse:collapse;width:100%;max-width:550px;">';

        // Name + Title with pink accent line
        $html .= '<tr><td style="padding-bottom:10px;border-bottom:3px solid #E91E8C;">';
        $html .= '<strong style="font-size:16px;color:#1a1a2e;letter-spacing:0.5px;">' . htmlspecialchars($name) . '</strong>';
        if ($title) $html .= '<br><span style="font-size:12px;color:#777;margin-top:2px;display:inline-block;">' . htmlspecialchars($title) . '</span>';
        $html .= '</td></tr>';

        // Contact numbers row - each label a unique colour
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

        // Thin elegant divider between contacts and company
        $html .= '<tr><td style="padding:0;"><div style="border-top:1px solid #eee;"></div></td></tr>';

        // Company row - bigger font
        if ($company) {
            $html .= '<tr><td style="padding-top:10px;">';
            $html .= '<strong style="font-size:14px;color:#1a1a2e;">' . htmlspecialchars($company) . '</strong>';
            if ($website) {
                $html .= ' <span style="color:#ccc;">|</span> <a href="' . $websiteUrl . '" style="font-size:13px;color:#0066CC;text-decoration:none;">' . htmlspecialchars($website) . '</a>';
            }
            $html .= '</td></tr>';
        }

        // Slogan row - pink, italic (no bold), elegant
        if ($slogan) {
            $html .= '<tr><td style="padding-top:4px;">';
            $html .= '<em style="font-size:12px;color:#E91E8C;font-style:italic;letter-spacing:0.3px;">' . htmlspecialchars($slogan) . '</em>';
            $html .= '</td></tr>';
        }

        // Banner image (if uploaded)
        if ($bannerUrl) {
            $html .= '<tr><td style="padding-top:14px;">';
            $html .= '<img src="' . htmlspecialchars($bannerUrl) . '" alt="Signature Banner" style="max-width:100%;height:auto;border-radius:4px;">';
            $html .= '</td></tr>';
        }

        // Subtle separator before logo
        $html .= '<tr><td style="padding-top:14px;"><div style="border-top:1px solid #e0e0e0;"></div></td></tr>';

        // Company logo with protection - links to website
        $logoLinkStart = $websiteUrl ? '<a href="' . $websiteUrl . '" target="_blank" style="text-decoration:none;">' : '';
        $logoLinkEnd = $websiteUrl ? '</a>' : '';
        $html .= '<tr><td style="padding-top:10px;">';
        $html .= $logoLinkStart;
        $html .= '<img src="' . $logoUrl . '" alt="CIMS" style="max-width:280px;height:auto;pointer-events:none;-webkit-user-drag:none;user-select:none;-moz-user-select:none;-webkit-user-select:none;-ms-user-select:none;" draggable="false" oncontextmenu="return false;" ondragstart="return false;">';
        $html .= $logoLinkEnd;
        $html .= '</td></tr>';

        $html .= '</table>';

        return $html;
    }

    /**
     * Signature editor page
     */
    public function signature(Request $request)
    {
        $editId = $request->get('edit_id');
        if ($editId) {
            $signature = DB::table('cims_email_signatures')->where('id', $editId)->first();
        } else {
            $signature = $this->getUserSignature();
        }

        // Get all signatures for the list
        $allSignatures = DB::table('cims_email_signatures')
            ->leftJoin('users', 'cims_email_signatures.user_id', '=', 'users.id')
            ->select('cims_email_signatures.*', 'users.first_name', 'users.last_name', 'users.email as user_email')
            ->orderBy('cims_email_signatures.full_name')
            ->get();

        // Get banner image URL from global settings
        $bannerImageUrl = '';
        try {
            $bannerImageUrl = DB::table('cims_email_settings')
                ->where('setting_key', 'signature_banner_url')
                ->value('setting_value') ?? '';
        } catch (\Exception $e) {}

        // Get global disclaimer text for preview
        $disclaimerText = '';
        try {
            $disclaimerText = DB::table('cims_email_settings')
                ->where('setting_key', 'disclaimer_html')
                ->value('setting_value') ?? '';
        } catch (\Exception $e) {}

        $counts = $this->getFolderCounts();

        return view('cims_email::emails.signature', compact('signature', 'allSignatures', 'counts', 'editId', 'bannerImageUrl', 'disclaimerText'));
    }

    /**
     * Save signature
     */
    public function saveSignature(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:200',
            'designation' => 'required|string|max:200',
        ]);

        // Determine which user's signature we're editing
        $editId = $request->input('edit_id');
        $userId = Auth::id();

        if ($editId) {
            $existing = DB::table('cims_email_signatures')->where('id', $editId)->first();
            if ($existing) {
                $userId = $existing->user_id;
            }
        } else {
            $existing = DB::table('cims_email_signatures')->where('user_id', $userId)->first();
        }

        $data = [
            'user_id' => $userId,
            'full_name' => $request->full_name,
            'designation' => $request->designation,
            'phone' => $request->phone ?? '',
            'mobile' => $request->mobile ?? '',
            'whatsapp' => $request->whatsapp ?? '',
            'direct_number' => $request->direct_number ?? '',
            'company_name' => $request->company_name ?? '',
            'company_website' => $request->company_website ?? '',
            'slogan' => $request->slogan ?? '',
            'disclaimer_html' => $request->disclaimer_html ?? '',
            'signature_html' => $request->signature_html ?? '',
            'is_active' => 1,
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('cims_email_signatures')->where('id', $existing->id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('cims_email_signatures')->insert($data);
        }

        return redirect()->route('cimsemail.signature')->with('success', 'Email signature saved!');
    }

    /**
     * Delete a signature
     */
    public function deleteSignature($id)
    {
        DB::table('cims_email_signatures')->where('id', $id)->delete();
        return redirect()->route('cimsemail.signature')->with('success', 'Signature deleted.');
    }

    /**
     * Get signature HTML (AJAX - for compose page)
     */
    public function getSignatureHtml()
    {
        $signature = $this->getUserSignature();
        $html = $this->buildSignatureHtml($signature);
        return response()->json(['html' => $html]);
    }

    // =========================================================================
    // CONTACT MANAGEMENT
    // =========================================================================

    /**
     * Contacts list page (card grid)
     */
    public function contacts(Request $request)
    {
        $search = $request->get('search', '');
        $clientFilter = $request->get('client_id');
        $showAll = $request->get('show_all', 0);

        $query = DB::table('cims_master_contacts')
            ->leftJoin('client_master', 'cims_master_contacts.client_id', '=', 'client_master.client_id')
            ->select(
                'cims_master_contacts.*',
                'client_master.company_name',
                'client_master.client_code'
            );

        // Default: only active contacts, unless show_all=1
        if (!$showAll) {
            $query->where('cims_master_contacts.is_active', 1);
        }

        // Search across key fields
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cims_master_contacts.first_name', 'like', "%{$search}%")
                  ->orWhere('cims_master_contacts.last_name', 'like', "%{$search}%")
                  ->orWhere('cims_master_contacts.known_as', 'like', "%{$search}%")
                  ->orWhere('cims_master_contacts.email', 'like', "%{$search}%")
                  ->orWhere('client_master.company_name', 'like', "%{$search}%");
            });
        }

        // Filter by specific client
        if ($clientFilter) {
            $query->where('cims_master_contacts.client_id', $clientFilter);
        }

        $contacts = $query->orderBy('cims_master_contacts.first_name')
            ->orderBy('cims_master_contacts.last_name')
            ->paginate(24);

        // Client list for the filter dropdown
        $clients = DB::table('client_master')
            ->where('is_active', 1)
            ->orderBy('company_name')
            ->get(['client_id', 'client_code', 'company_name']);

        $counts = $this->getFolderCounts();

        return view('cims_email::emails.contacts', compact('contacts', 'search', 'clientFilter', 'showAll', 'clients', 'counts'));
    }

    /**
     * Create new contact
     */
    public function storeContact(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'contact_' . Str::random(20) . '.' . $file->getClientOriginalExtension();
            $storagePath = base_path('../storage/contact_photos');
            if (!is_dir($storagePath)) {
                mkdir($storagePath, 0755, true);
            }
            $file->move($storagePath, $filename);
            $photoPath = $filename;
        }

        DB::table('cims_master_contacts')->insert([
            'client_id' => $request->client_id,
            'title' => $request->title ?? null,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'known_as' => $request->known_as ?? null,
            'gender' => $request->gender ?? null,
            'email' => $request->email ?? null,
            'phone' => $request->phone ?? null,
            'mobile' => $request->mobile ?? null,
            'whatsapp' => $request->whatsapp ?? null,
            'position' => $request->position ?? null,
            'department' => $request->department ?? null,
            'notes' => $request->notes ?? null,
            'photo' => $photoPath,
            'source' => 'manual',
            'is_active' => 1,
            'created_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('cimsemail.contacts')->with('success', 'Contact created successfully.');
    }

    /**
     * Update existing contact
     */
    public function updateContact(Request $request, $id)
    {
        $contact = DB::table('cims_master_contacts')->where('id', $id)->first();
        if (!$contact) {
            return back()->with('error', 'Contact not found.');
        }

        $request->validate([
            'client_id' => 'required|integer',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
        ]);

        $data = [
            'client_id' => $request->client_id,
            'title' => $request->title ?? null,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'known_as' => $request->known_as ?? null,
            'gender' => $request->gender ?? null,
            'email' => $request->email ?? null,
            'phone' => $request->phone ?? null,
            'mobile' => $request->mobile ?? null,
            'whatsapp' => $request->whatsapp ?? null,
            'position' => $request->position ?? null,
            'department' => $request->department ?? null,
            'notes' => $request->notes ?? null,
            'is_active' => $request->has('is_active') ? 1 : ($contact->is_active ?? 1),
            'updated_at' => now(),
        ];

        // Handle photo upload if new one provided
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'contact_' . Str::random(20) . '.' . $file->getClientOriginalExtension();
            $storagePath = base_path('../storage/contact_photos');
            if (!is_dir($storagePath)) {
                mkdir($storagePath, 0755, true);
            }
            $file->move($storagePath, $filename);
            $data['photo'] = $filename;
        }

        DB::table('cims_master_contacts')->where('id', $id)->update($data);

        return redirect()->route('cimsemail.contacts')->with('success', 'Contact updated successfully.');
    }

    /**
     * Delete contact (soft delete - set is_active=0)
     */
    public function deleteContact($id)
    {
        $contact = DB::table('cims_master_contacts')->where('id', $id)->first();
        if (!$contact) {
            return back()->with('error', 'Contact not found.');
        }

        DB::table('cims_master_contacts')->where('id', $id)->update([
            'is_active' => 0,
            'updated_at' => now(),
        ]);

        return redirect()->route('cimsemail.contacts')->with('success', 'Contact deleted.');
    }

    /**
     * Get single contact for edit modal (AJAX)
     */
    public function getContact($id)
    {
        $contact = DB::table('cims_master_contacts')
            ->leftJoin('client_master', 'cims_master_contacts.client_id', '=', 'client_master.client_id')
            ->where('cims_master_contacts.id', $id)
            ->select(
                'cims_master_contacts.*',
                'client_master.company_name',
                'client_master.client_code'
            )
            ->first();

        if (!$contact) {
            return response()->json(['error' => 'Contact not found'], 404);
        }

        return response()->json($contact);
    }

    /**
     * Check for duplicate contact by email (AJAX)
     */
    public function checkDuplicateContact(Request $request)
    {
        $email = $request->input('email');
        $clientId = $request->input('client_id');
        $excludeId = $request->input('exclude_id'); // for edit mode

        $warnings = [];

        if (empty($email)) {
            return response()->json(['warnings' => $warnings]);
        }

        // Check same client - same email
        $sameClientQuery = DB::table('cims_master_contacts')
            ->where('email', $email)
            ->where('client_id', $clientId)
            ->where('is_active', 1);

        if ($excludeId) {
            $sameClientQuery->where('id', '!=', $excludeId);
        }

        $sameClientMatch = $sameClientQuery->first();

        if ($sameClientMatch) {
            $warnings[] = [
                'type' => 'same_client',
                'message' => 'This email already exists for this client: ' . trim($sameClientMatch->first_name . ' ' . $sameClientMatch->last_name),
            ];
        }

        // Check other clients - same email
        $otherClientsQuery = DB::table('cims_master_contacts')
            ->leftJoin('client_master', 'cims_master_contacts.client_id', '=', 'client_master.client_id')
            ->where('cims_master_contacts.email', $email)
            ->where('cims_master_contacts.is_active', 1);

        if ($clientId) {
            $otherClientsQuery->where('cims_master_contacts.client_id', '!=', $clientId);
        }

        if ($excludeId) {
            $otherClientsQuery->where('cims_master_contacts.id', '!=', $excludeId);
        }

        $otherClientsMatches = $otherClientsQuery
            ->select('cims_master_contacts.first_name', 'cims_master_contacts.last_name', 'client_master.company_name')
            ->get();

        foreach ($otherClientsMatches as $match) {
            $warnings[] = [
                'type' => 'cross_client',
                'message' => 'This email also exists under ' . ($match->company_name ?? 'Unknown Client') . ': ' . trim($match->first_name . ' ' . $match->last_name),
            ];
        }

        return response()->json(['warnings' => $warnings]);
    }
}
