<?php
// Direct PDO insert - bypasses Laravel auth
// Delete after use

$dsn = 'pgsql:host=localhost;port=5432;dbname=grow_crm_2026';
$user = '5fokp_qnbo1';
$pass = '4P9716bzm7598A';

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    die('DB Error: ' . $e->getMessage());
}

$now = date('Y-m-d H:i:s');

$templates = [
    [
        'Bank Statement Request – Friendly',
        '{current_month} {current_year} Bank Statement Required – {company_name}',
        '<p>Dear {contact_title} {contact_first_name},</p><p>I hope you are well.</p><p>As part of our ongoing monthly accounting and compliance process, we kindly request that you please forward us your <strong>{current_month} {current_year} bank statement</strong> at your earliest convenience.</p><p>Receiving the statement timeously allows us to ensure that your bookkeeping, reconciliations, and statutory submissions are processed accurately and without delay.</p><p>Kindly email the bank statement to <strong>{sender_email}</strong> once available.</p><p>If there are multiple accounts, please include all relevant statements for {current_month} {current_year}.</p><p>Should you have any questions or require assistance retrieving the statement from your bank, please feel free to let us know &mdash; we are always happy to assist.</p><p>Thank you for your continued cooperation and trust in our services.</p>',
        'Compliance',
    ],
    [
        'Bank Statement Request – Overdue',
        'FOLLOW-UP: Outstanding {current_month} {current_year} Bank Statement – {company_name}',
        '<p>Dear {contact_title} {contact_first_name},</p><p>I hope you are well.</p><p>We have not yet received your <strong>{current_month} {current_year} bank statement</strong> for <strong>{company_name}</strong>.</p><p>We are currently unable to finalise your accounting records and statutory submissions without it, which may result in compliance delays.</p><p>Kindly email the statement to <strong>{sender_email}</strong> as soon as possible to avoid any delays.</p><p>If there are multiple bank accounts, please include all relevant statements.</p><p>Please confirm once sent.</p><p>Thank you for your immediate attention to this matter.</p>',
        'Compliance',
    ],
    [
        'Bank Statement Request – Urgent',
        'URGENT: Outstanding Bank Statement – Action Required – {company_name}',
        '<p>Dear {contact_title} {contact_first_name},</p><p>This is an <strong>urgent reminder</strong> regarding the outstanding bank statement for <strong>{company_name}</strong> ({client_code}).</p><p>We have not received your <strong>{current_month} {current_year} bank statement</strong> despite previous requests. Without this document, we are unable to:</p><ul><li>Complete monthly bookkeeping and reconciliations</li><li>Submit VAT returns timeously</li><li>Process EMP201 and other statutory obligations</li></ul><p><strong>Failure to submit may result in penalties and interest from SARS.</strong></p><p>Kindly email all outstanding bank statements to <strong>{sender_email}</strong> as a matter of urgency.</p><p>If you are experiencing any difficulty obtaining the statement from your bank, please let us know immediately so we can assist.</p><p>Thank you for your urgent attention.</p>',
        'Compliance',
    ],
];

$inserted = 0;
$sql_check = "SELECT COUNT(*) FROM cims_email_templates WHERE name = :name";
$sql_insert = "INSERT INTO cims_email_templates (name, subject, body_html, category, is_active, created_by, created_at, updated_at) VALUES (:name, :subject, :body_html, :category, 1, 1, :created_at, :updated_at)";

foreach ($templates as $t) {
    $stmt = $pdo->prepare($sql_check);
    $stmt->execute(['name' => $t[0]]);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare($sql_insert);
        $stmt->execute([
            'name' => $t[0],
            'subject' => $t[1],
            'body_html' => $t[2],
            'category' => $t[3],
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $inserted++;
        echo "Inserted: {$t[0]}\n";
    } else {
        echo "Exists: {$t[0]}\n";
    }
}

echo "\nDone. Inserted {$inserted} template(s).\n";

// Show all templates
$stmt = $pdo->query("SELECT id, name, category, is_active FROM cims_email_templates ORDER BY id");
echo "\nAll templates:\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  #{$row['id']} [{$row['category']}] {$row['name']} (active: {$row['is_active']})\n";
}
