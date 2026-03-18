<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$templates = DB::table('cims_email_templates')->where('is_active', 1)->get(['id','name','category','subject','body_html']);
foreach($templates as $t) {
    echo "=== Template ID: {$t->id} | {$t->name} ({$t->category}) ===\n";
    echo "Subject: {$t->subject}\n";
    echo "Body (first 500 chars):\n" . substr($t->body_html ?? '', 0, 500) . "\n\n";
}
