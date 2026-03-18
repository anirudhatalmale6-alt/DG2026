<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate building signature for user 1
$signature = DB::table('cims_email_signatures')->where('user_id', 1)->where('is_active', 1)->first();

if (!$signature) {
    echo "NO SIGNATURE FOUND for user_id=1\n";
    exit;
}

echo "Signature found: {$signature->full_name}\n\n";

// Check if buildSignatureHtml would return anything
if (!empty($signature->signature_html)) {
    echo "Has custom HTML\n";
} else {
    echo "Using auto-generated signature\n";
    $name = $signature->full_name ?? '';
    echo "Name: '{$name}' (empty=" . (empty($name) ? 'YES' : 'NO') . ")\n";

    // The function returns '' if name is empty
    if (empty($name)) {
        echo "PROBLEM: Name is empty, buildSignatureHtml returns empty string!\n";
    } else {
        echo "Name is not empty, signature should generate\n";
    }
}

// Now test with the actual controller method via reflection
$controller = app()->make('Modules\CIMS_Email\Http\Controllers\EmailController');
$method = new ReflectionMethod($controller, 'buildSignatureHtml');
$method->setAccessible(true);
$html = $method->invoke($controller, $signature);

echo "\n=== GENERATED HTML LENGTH: " . strlen($html) . " ===\n";
if (strlen($html) > 0) {
    echo "First 500 chars:\n" . substr($html, 0, 500) . "\n";
} else {
    echo "EMPTY - signature HTML is blank!\n";
}
