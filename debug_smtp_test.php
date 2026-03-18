<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$basePath = '/usr/www/users/smartucbmh/application';
require $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "<pre>";

// Check what SMTP settings are saved
try {
    $settings = \Illuminate\Support\Facades\DB::table('cims_email_settings')
        ->pluck('setting_value', 'setting_key')
        ->toArray();

    echo "Saved SMTP Settings:\n";
    foreach ($settings as $k => $v) {
        if ($k === 'smtp_password') {
            echo "  {$k}: " . str_repeat('*', strlen($v)) . " (" . strlen($v) . " chars)\n";
        } elseif ($k === 'disclaimer_html') {
            echo "  {$k}: " . substr($v, 0, 50) . "... (" . strlen($v) . " chars)\n";
        } else {
            echo "  {$k}: {$v}\n";
        }
    }

    // Try setting config and sending
    echo "\nAttempting SMTP connection...\n";

    \Illuminate\Support\Facades\Config::set('mail.default', 'smtp');
    \Illuminate\Support\Facades\Config::set('mail.mailers.smtp.host', $settings['smtp_host'] ?? '');
    \Illuminate\Support\Facades\Config::set('mail.mailers.smtp.port', (int)($settings['smtp_port'] ?? 587));
    \Illuminate\Support\Facades\Config::set('mail.mailers.smtp.encryption', ($settings['smtp_encryption'] ?? '') ?: null);
    \Illuminate\Support\Facades\Config::set('mail.mailers.smtp.username', $settings['smtp_username'] ?? '');
    \Illuminate\Support\Facades\Config::set('mail.mailers.smtp.password', $settings['smtp_password'] ?? '');

    app('mail.manager')->purge('smtp');

    $fromEmail = $settings['from_email'] ?? $settings['smtp_username'] ?? '';
    $fromName = $settings['from_name'] ?? 'SmartWeigh CIMS';

    echo "From: {$fromName} <{$fromEmail}>\n";
    echo "Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "Port: " . config('mail.mailers.smtp.port') . "\n";
    echo "Encryption: " . config('mail.mailers.smtp.encryption') . "\n";

    // Try to create the transport and connect
    $transport = app('mail.manager')->mailer('smtp')->getSymfonyTransport();
    echo "Transport class: " . get_class($transport) . "\n";

    \Illuminate\Support\Facades\Mail::raw('SMTP Test from debug script - ' . date('Y-m-d H:i:s'), function ($message) use ($fromEmail, $fromName) {
        $message->from($fromEmail, $fromName);
        $message->to($fromEmail);
        $message->subject('CIMS Debug SMTP Test');
    });

    echo "\nSUCCESS - Test email sent!\n";

} catch (\Throwable $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>";
$kernel->terminate($request, $response);
