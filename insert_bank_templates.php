<?php
/**
 * Insert Bank Statement Request Templates into CIMS Email
 * Run once then delete
 */

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$now = now();

// Template 1: Friendly Initial Request
$tpl1_body = '<p>Dear {contact_title} {contact_first_name},</p>

<p>I hope you are well.</p>

<p>As part of our ongoing monthly accounting and compliance process, we kindly request that you please forward us your <strong>{current_month} {current_year} bank statement</strong> at your earliest convenience.</p>

<p>Receiving the statement timeously allows us to ensure that your bookkeeping, reconciliations, and statutory submissions are processed accurately and without delay.</p>

<p>Kindly email the bank statement to <strong>{sender_email}</strong> once available.</p>

<p>If there are multiple accounts, please include all relevant statements for {current_month} {current_year}.</p>

<p>Should you have any questions or require assistance retrieving the statement from your bank, please feel free to let us know &mdash; we are always happy to assist.</p>

<p>Thank you for your continued cooperation and trust in our services.</p>';

// Template 2: Overdue Follow-Up
$tpl2_body = '<p>Dear {contact_title} {contact_first_name},</p>

<p>I hope you are well.</p>

<p>We have not yet received your <strong>{current_month} {current_year} bank statement</strong> for <strong>{company_name}</strong>.</p>

<p>We are currently unable to finalise your accounting records and statutory submissions without it, which may result in compliance delays.</p>

<p>Kindly email the statement to <strong>{sender_email}</strong> as soon as possible to avoid any delays.</p>

<p>If there are multiple bank accounts, please include all relevant statements.</p>

<p>Please confirm once sent.</p>

<p>Thank you for your immediate attention to this matter.</p>';

// Template 3: Urgent / Compliance Risk
$tpl3_body = '<p>Dear {contact_title} {contact_first_name},</p>

<p>This is an <strong>urgent reminder</strong> regarding the outstanding bank statement for <strong>{company_name}</strong> ({client_code}).</p>

<p>We have not received your <strong>{current_month} {current_year} bank statement</strong> despite previous requests. Without this document, we are unable to:</p>

<ul>
<li>Complete monthly bookkeeping and reconciliations</li>
<li>Submit VAT returns timeously</li>
<li>Process EMP201 and other statutory obligations</li>
</ul>

<p><strong>Failure to submit may result in penalties and interest from SARS.</strong></p>

<p>Kindly email all outstanding bank statements to <strong>{sender_email}</strong> as a matter of urgency.</p>

<p>If you are experiencing any difficulty obtaining the statement from your bank, please let us know immediately so we can assist.</p>

<p>Thank you for your urgent attention.</p>';

$templates = [
    [
        'name' => 'Bank Statement Request – Friendly',
        'subject' => '{current_month} {current_year} Bank Statement Required – {company_name}',
        'body_html' => $tpl1_body,
        'category' => 'Compliance',
        'is_active' => 1,
        'created_by' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ],
    [
        'name' => 'Bank Statement Request – Overdue',
        'subject' => 'FOLLOW-UP: Outstanding {current_month} {current_year} Bank Statement – {company_name}',
        'body_html' => $tpl2_body,
        'category' => 'Compliance',
        'is_active' => 1,
        'created_by' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ],
    [
        'name' => 'Bank Statement Request – Urgent',
        'subject' => 'URGENT: Outstanding Bank Statement – Action Required – {company_name}',
        'body_html' => $tpl3_body,
        'category' => 'Compliance',
        'is_active' => 1,
        'created_by' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ],
];

$inserted = 0;
foreach ($templates as $tpl) {
    // Check if already exists
    $exists = DB::table('cims_email_templates')->where('name', $tpl['name'])->exists();
    if (!$exists) {
        DB::table('cims_email_templates')->insert($tpl);
        $inserted++;
        echo "Inserted: {$tpl['name']}\n";
    } else {
        echo "Already exists: {$tpl['name']}\n";
    }
}

echo "\nDone. Inserted {$inserted} template(s).\n";
