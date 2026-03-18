<?php
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$email = DB::table('cims_emails')
    ->where('folder', 'sent')
    ->orderByDesc('id')
    ->first();

header('Content-Type: text/plain');
if ($email) {
    echo "ID: " . $email->id . "\n";
    echo "Subject: " . $email->subject . "\n";
    echo "TO: " . $email->to_emails . "\n";
    echo "CC: " . $email->cc_emails . "\n";
    echo "BCC: " . $email->bcc_emails . "\n";
    echo "Status: " . $email->status . "\n";
    echo "Sent at: " . $email->sent_at . "\n";
    echo "Client ID: " . $email->client_id . "\n";
} else {
    echo "No sent emails found.";
}
